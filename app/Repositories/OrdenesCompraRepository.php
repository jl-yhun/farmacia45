<?php

namespace App\Repositories;

use App\Classes\CierreAperturaCaja;
use App\Classes\ICierreAperturaCajaBuilder;
use App\Classes\IStockManager;
use App\Configuracion;
use App\Enums\OrdenCompraEstado;
use App\Helpers\LoggerBuilder;
use App\OrdenCompra;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

class OrdenesCompraRepository implements IOrdenesCompraRepository
{
    private $_aperturaCaja;
    private $_logger;
    private $_productosRepository;
    private $_stockManager;
    private $_model;

    public function __construct(
        IAperturasCajaRepository $aperturaCaja,
        LoggerBuilder $logger,
        IProductosRepository $productosRepository,
        IStockManager $stockManager,
        OrdenCompra $model
    ) {
        $this->_aperturaCaja = $aperturaCaja;
        $this->_logger = $logger;
        $this->_productosRepository = $productosRepository;
        $this->_stockManager = $stockManager;
        $this->_model = $model;
    }

    public function getOrdered()
    {

        return OrdenCompra::orderByRaw("(CASE 
        when estado = '" . OrdenCompraEstado::Pendiente->value . "' THEN 0
        when estado = '" . OrdenCompraEstado::Pedido->value . "' THEN 1
        when estado = '" . OrdenCompraEstado::Recibido->value . "' THEN 2
        ELSE 3
        END)")->orderBy('id', 'desc')->take(50)->get();
    }

    public function show($id)
    {
        return $this->_model::find($id);
    }

    public function suggested($id = 0)
    {
        $currentAperturaCajaId = $id == 0 ? $this->_aperturaCaja->getLast() :
            $this->_aperturaCaja->show($id);
        return DB::select(DB::raw("SELECT similares, proveedores,
                            id,
                            SUM(sugerido) sugerido, 
                            codigo_barras, nombre, 
                            descripcion, apertura_caja_id,
                            stock
FROM(SELECT * FROM view_suggested_purchase 
WHERE apertura_caja_id = {$currentAperturaCajaId->id}) t 
GROUP BY id, codigo_barras, nombre, descripcion, stock, apertura_caja_id, similares
ORDER BY stock"));
    }

    public function notAvailable($id = 0)
    {
        $currentAperturaCajaId = $id == 0 ? $this->_aperturaCaja->getLast() :
            $this->_aperturaCaja->show($id);
        return DB::select(DB::raw("SELECT p.*, (SELECT COUNT(*) 
                                                  FROM productos_similares ps 
                                                 WHERE ps.base_producto_id = p.id) similares,
                                               (SELECT COUNT(*) 
                                                  from productos_proveedores pp 
                                                 where pp.producto_id = p.id and 
                                                       pp.disponible = 1) proveedores
        FROM productos p WHERE
             stock = 0 AND 
             deleted_at IS NULL AND
             codigo_barras NOT IN(SELECT codigo_barras 
                                    FROM view_suggested_purchase 
                                   WHERE apertura_caja_id = {$currentAperturaCajaId->id}) ORDER BY nombre;"));
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $oc = OrdenCompra::create([
                ...$data,
                'creador_id' => auth()->user()->id,
                'estado' => OrdenCompraEstado::Pendiente->value,
                'apertura_caja_id' => $this->_aperturaCaja->getCurrent()->id ?? null
            ]);

            DB::commit();

            $this->_logger
                ->success()
                ->description('Ok al guardar la orden de compra.')
                ->user_id(auth()->user()->id)
                ->link_id($oc->id)
                ->module($this::class)
                ->method('create')
                ->after(json_encode($data))
                ->log();
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al guardar la orden de compra.')
                ->exception($e)
                ->module($this::class)
                ->method('create')
                ->user_id(auth()->user()->id)
                ->before(json_encode($data))
                ->log();

            throw $e;
        }
    }

    private function getLastPendingByProveedor($proveedorId)
    {
        return $this->_model->where('proveedor_id', $proveedorId)
            ->where('estado', OrdenCompraEstado::Pendiente->value)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function tryIncreaseAmountIfExist($ordenCompra, $itemData)
    {
        $producto = $ordenCompra
            ->productos()
            ->where('producto_id', $itemData['producto_id'])
            ->first();

        if ($producto == null)
            return false;

        $producto->pivot->cantidad += $itemData['cantidad'];

        $producto->pivot->save();
        return true;
    }

    public function addItem($itemData)
    {
        try {
            DB::beginTransaction();

            $currentOc = $this->getLastPendingByProveedor($itemData['proveedor_id']);

            if (!$currentOc) {
                $this->create($itemData);
                $currentOc = $this->getLastPendingByProveedor($itemData['proveedor_id']);
            }

            if ($this->tryIncreaseAmountIfExist($currentOc, $itemData)) {
                DB::commit();

                $this->_logger
                    ->success()
                    ->description('tryIncreaseAmountIfExist finishes')
                    ->user_id(Auth::user()->id)
                    ->module($this::class)
                    ->method('addItem')
                    ->link_id($currentOc->id)
                    ->after(json_encode($itemData))
                    ->log();
                return;
            }

            $productoProveedor = $this->_productosRepository
                ->showByProveedor($itemData['producto_id'], $itemData['proveedor_id']);

            $currentOc->productos()->syncWithoutDetaching([$itemData['producto_id'] => [
                'cantidad' => $itemData['cantidad'],
                'compra' => $productoProveedor->pivot->precio ?? 0
            ]]);

            DB::commit();

            $this->_logger
                ->success()
                ->description('Ok al agregar item a la orden de compra.')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->method('addItem')
                ->link_id($currentOc->id)
                ->after(json_encode($itemData))
                ->log();
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al agregar item a la orden de compra.')
                ->exception($e)
                ->module($this::class)
                ->method('addItem')
                ->user_id(Auth::user()->id)
                ->before(json_encode($itemData))
                ->log();
            throw $e;
        }
    }

    public function patchItem($ocId, $productId, $itemData)
    {
        try {
            DB::beginTransaction();
            $orden = $this->show($ocId);

            $productoProveedor = $this->_productosRepository
                ->showByProveedor($productId, $orden->proveedor_id);

            $producto = $this->_productosRepository
                ->show($productId);

            $orden->productos()->syncWithoutDetaching([$productId => [
                'cantidad' => $itemData['cantidad'],
                'compra' => $itemData['compra']
                    ?? $productoProveedor->pivot->precio
                    ?? $producto->compra
            ]]);

            DB::commit();

            $this->_logger
                ->success()
                ->description('Ok al actualizar item de la orden de compra.')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->method('patchItem')
                ->link_id($ocId)
                ->after(json_encode($itemData))
                ->log();
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al actualizar item de la orden de compra.')
                ->exception($e)
                ->module($this::class)
                ->method('patchItem')
                ->link_id($ocId)
                ->user_id(Auth::user()->id)
                ->before(json_encode($itemData))
                ->log();
            throw $e;
        }
    }

    public function deleteItem($ocId, $productId)
    {
        try {
            DB::beginTransaction();
            $orden = $this->show($ocId);

            $orden->productos()->detach($productId);

            DB::commit();

            $this->_logger
                ->success()
                ->description('Ok al eliminar item de la orden de compra.')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->method('deleteItem')
                ->link_id($ocId)
                ->log();
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al eliminar item de la orden de compra.')
                ->exception($e)
                ->module($this::class)
                ->method('deleteItem')
                ->link_id($ocId)
                ->user_id(auth()->user()->id)
                ->log();
            throw $e;
        }
    }

    public function patch($ocId, $input)
    {
        try {
            DB::beginTransaction();
            $orden = $this->show($ocId);

            foreach ($input as $key => $value) {
                $orden->{$key} = $value;
            }

            $this->_stockManager->tryAdjustStockFromOrderProducts($orden, $this->_productosRepository);

            $orden->save();

            DB::commit();

            $this->_logger
                ->success()
                ->description('Se actualizó la orden de compra correctamente.')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->method('patch')
                ->link_id($ocId)
                ->after(json_encode($input))
                ->log();
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al actualizar la orden de compra.')
                ->exception($e)
                ->module($this::class)
                ->method('patch')
                ->link_id($ocId)
                ->user_id(auth()->user()->id)
                ->before(json_encode($input))
                ->log();
            throw $e;
        }
    }
}
