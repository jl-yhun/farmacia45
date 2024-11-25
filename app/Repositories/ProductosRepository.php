<?php

namespace App\Repositories;

use App\Classes\ISimilaresCombinator;
use App\Classes\IStockManager;
use App\Classes\ProductosQuery;
use App\Classes\ProductosQuery\ProductosQueryBuilder;
use App\Events\MinStockReached;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\CustomValidator;
use App\Helpers\LoggerBuilder;
use App\Helpers\SanitizerBuilder;
use App\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\InputBag;

class ProductosRepository implements IProductosRepository
{
    private $_logger;
    private $_stockManager;
    private $_sanitizer;
    private $_productosGranelRepository;

    public function __construct(
        LoggerBuilder $logger,
        IStockManager $stockManager,
        SanitizerBuilder $sanitizer,
        IProductosGranelRepository $productosGranelRepository
    ) {
        $this->_logger = $logger;
        $this->_stockManager = $stockManager;
        $this->_sanitizer = $sanitizer;
        $this->_productosGranelRepository = $productosGranelRepository;
    }

    public function all() {
        return Producto::all();
    }

    public function get(InputBag $queryParams)
    {
        $queryObj = ProductosQueryBuilder::buildFromQueryParams($queryParams);

        $productos = DB::select($queryObj->finish());

        $resultSet = [];

        foreach ($productos as $producto) {
            $tags = DB::select(<<<EOT
            SELECT t.nombre, t.id
            FROM tags_models tm
            INNER JOIN tags t ON t.id = tm.tag_id
            WHERE tm.tageable_id = :productoId 
           EOT, ['productoId' => $producto->id]);

            $resultSet[] = [...get_object_vars($producto), 'tags' => $tags];
        }

        return $resultSet;
    }

    public function search($searchText)
    {
        $searchText = $this->_sanitizer
            ->rmAcentos()
            ->trim()
            ->doUpperCase()
            ->trim()
            ->apply($searchText);

        return Producto::where(function ($q) use ($searchText) {
            $q->where('nombre', 'like', "%$searchText%")
                ->orWhere('descripcion', 'like', "%$searchText%")
                ->orWhere('codigo_barras', 'like', "%$searchText%");
        })->orderBy('nombre')->get();
    }

    public function getOrderedBy($property)
    {
        return Producto::orderBy($property)->get();
    }

    public function modifyStockByAmount($producto, $amount = -1)
    {
        if (($producto->stock + $amount) < 0)
            throw new StockLessThanZeroException('Stock no puede ser menor a 0.');
        $producto->stock += $amount;
        $producto->timestamps = false;
        $producto->save();

        $this->_stockManager->tryTriggerMinStockReachedEvent($producto);
    }

    public function show($id)
    {
        return Producto::find($id);
    }

    public function showByProveedor($id, $proveedor_id)
    {
        return Producto::find($id)->proveedores()
            ->where('id', $proveedor_id)
            ->first();
    }

    public function showByCode($codigo_barras)
    {
        $prod = Producto::where('codigo_barras', $codigo_barras)->first();
        if (!$prod)
            throw new ProductNotFoundException();

        return $prod;
    }

    public function create($producto)
    {
        try {
            DB::beginTransaction();
            $productoToSave = Producto::create($producto);
            $this->createCodigoBarrasIfNotGiven($productoToSave);
            $this->_productosGranelRepository->tryCreateProductoGranel($productoToSave, $producto);
            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($producto))
                ->log();

            $this->makePurchasePriceVisibleTemporally($productoToSave);
            return $productoToSave;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('agregar')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->before(json_encode($producto))
                ->exception($e)
                ->log();

            throw $e;
        }
    }

    public function update($data, $prod)
    {
        return $this->doUpdate($data, $prod);
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Producto::destroy($id);
            DB::commit();

            $this->_logger
                ->success('eliminar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('eliminar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->exception($e)
                ->log();
        }

        return false;
    }

    public function linkProveedor($id, $input)
    {
        try {
            DB::beginTransaction();

            $producto = $this->show($id);

            $producto->proveedores()->attach($input['proveedor_id'], [
                'codigo' => strtoupper(trim($input['codigo'])),
                'disponible' => $input['disponible'],
                'precio' => $input['precio'],
                'default' => $input['default'] ?? false,
            ]);

            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('linkProveedor')
                ->after(json_encode($input))
                ->log();

            return $this->findLinkedDealerById($producto, $input);
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->_logger
                ->error('agregar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('linkProveedor')
                ->before(json_encode($input))
                ->exception($th)
                ->log();

            throw $th;
        }
    }

    public function updateLinkedProveedor($id, $proveedorId, $input)
    {
        try {
            DB::beginTransaction();

            $producto = $this->show($id);

            $producto->proveedores()->updateExistingPivot(
                $proveedorId,
                [
                    'codigo' => strtoupper(trim($input['codigo'])),
                    'disponible' => $input['disponible'],
                    'precio' => $input['precio'],
                    'default' => $input['default'] ?? false,
                ]
            );

            DB::commit();

            $this->_logger
                ->success('editar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('updateLinkedProveedor')
                ->after(json_encode($input))
                ->log();

            return $this->findLinkedDealerById($producto, $input);
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->_logger
                ->error('editar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('updateLinkedProveedor')
                ->before(json_encode($input))
                ->exception($th)
                ->log();

            throw $th;
        }
    }

    public function deleteLinkedProveedor($id, $proveedorId)
    {
        try {
            DB::beginTransaction();

            $producto = $this->show($id);

            $producto->proveedores()->detach($proveedorId);

            DB::commit();

            $this->_logger
                ->success('eliminar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('deleteLinkedProveedor')
                ->log();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->_logger
                ->error('eliminar')
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->module($this::class)
                ->method('deleteLinkedProveedor')
                ->exception($th)
                ->log();

            throw $th;
        }
    }

    public function setUpInventory($producto_id, $diff)
    {
        $producto = $this->show($producto_id);

        $data = [
            'stock' => $producto->stock + $diff['stock'],
            'compra' => $producto->compra + $diff['compra'],
            'venta' => $producto->venta + $diff['venta']
        ];

        $this->doUpdate($data, $producto);
    }

    public function removeStock($codigo_barras)
    {
        $this->updateByCode([
            'stock' => 0
        ], $codigo_barras);
    }


    public function makePurchasePriceVisibleTemporally(&$producto)
    {
        $producto->makeVisible(['compra']);
    }


    /**
     * private methos
     */

    private function updateByCode($data, $codigo_barras)
    {
        $prod = $this->showByCode($codigo_barras);
        $this->doUpdate($data, $prod);
    }

    private function doUpdate($data, $dato)
    {
        try {
            $persistentProduct = $dato->toArray();

            DB::beginTransaction();

            $dato->update($data);
            $this->_productosGranelRepository->tryCreateProductoGranel($dato, $data);

            DB::commit();

            $this->_logger
                ->success('actualizar')
                ->user_id(Auth::user()->id)
                ->link_id($dato->id)
                ->module($this::class)
                ->before(json_encode($persistentProduct))
                ->after(json_encode($data))
                ->log();

            if ($this->wasStockUpdated($data))
                $this->_stockManager->tryTriggerMinStockReachedEvent($dato);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('actualizar')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->exception($e)
                ->log();
        }

        return false;
    }

    private function wasStockUpdated(array $data): bool
    {
        return key_exists('stock', $data);
    }

    private function createCodigoBarrasIfNotGiven($producto)
    {
        if (!isset($producto->codigo_barras))
            $producto->codigo_barras = str_pad($producto->id, 5, '0', STR_PAD_LEFT) . '-IN';

        $producto->save();
    }

    private function findLinkedDealerById($producto, $input)
    {
        return $producto->proveedores()
            ->where('id', $input['proveedor_id'])
            ->first() ?? null;
    }
}
