<?php

namespace App\Repositories;

use App\Classes\ISummaryInventario;
use App\Classes\SummaryInventario;
use App\Enums\TipoInventario;
use App\Enums\TipoResumenInventario;
use App\Exceptions\ProductNotFoundException;
use App\Helpers\LoggerBuilder;
use App\Inventario;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\InputBag;

class InventarioRepository implements IInventarioRepository
{
    private $_logger;
    private $_productosRepository;
    private $_summaryInventario;

    public function __construct(
        LoggerBuilder $logger,
        IProductosRepository $productosRepository,
        ISummaryInventario $summaryInventario
    ) {
        $this->_logger = $logger;
        $this->_productosRepository = $productosRepository;
        $this->_summaryInventario = $summaryInventario;
    }

    public function get()
    {
        return $this->getAllUnprocessed();
    }

    public function show($codigo_barras)
    {
        $producto = $this->_productosRepository->showByCode($codigo_barras);
        return Inventario::with('producto')
            ->where('producto_id', $producto->id)->first();
    }

    public function update($data, $codigo_barras)
    {
        try {
            DB::beginTransaction();

            $inventario = $this->show($codigo_barras);

            $forLog = $inventario->toArray();

            $inventario->update([
                ...$data
            ]);

            DB::commit();

            $this->_logger
                ->success()
                ->module($this::class)
                ->before(json_encode($forLog))
                ->after(json_encode($data))
                ->user_id(auth()->user()->id)
                ->description('Registro de inventario actualizado exitosamente.')
                ->log();

            return true;
        } catch (Exception $e) {

            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->before(json_encode($data))
                ->user_id(auth()->user()->id)
                ->description('Error al actualizar item en el inventario.')
                ->log();
        }

        return false;
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();

            $inventario = $this->show($data['codigo_barras']);

            if (!$inventario) {
                $producto = $this->_productosRepository->showByCode($data['codigo_barras']);;

                Inventario::create([
                    'cantidad' => 1,
                    'producto_id' => $producto->id,
                    'venta' => $producto->venta,
                    'compra' => $producto->compra
                ]);
            } else {
                $inventario->cantidad++;
                $inventario->save();
            }


            DB::commit();
        } catch (ProductNotFoundException $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->before(json_encode($data))
                ->user_id(auth()->user()->id)
                ->description('Producto no existe.')
                ->log();

            throw $e;
        } catch (Exception $e) {

            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->before(json_encode($data))
                ->user_id(auth()->user()->id)
                ->description('Error al registrar item en el inventario.')
                ->log();
            throw $e;
        }
    }

    public function destroy($codigo_barras)
    {
        try {
            DB::beginTransaction();
            $inventario = $this->show($codigo_barras);
            $preserve = $inventario->toArray();

            $inventario->delete();

            DB::commit();

            $this->_logger
                ->success()
                ->module($this::class)
                ->before(json_encode($preserve))
                ->user_id(auth()->user()->id)
                ->description('Registro de inventario eliminado exitosamente.')
                ->log();

            return true;
        } catch (Exception $e) {

            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->before($codigo_barras)
                ->user_id(auth()->user()->id)
                ->description('Error al eliminar item del inventario.')
                ->log();
        }

        return false;
    }

    private function setAllProcessed()
    {
        Inventario::where('procesado', 0)
            ->update(['procesado' => 1]);
    }

    public function diff()
    {
        try {
            $items = $this->prepareForCalculation();
            $inDb = $this->_productosRepository->all();

            return $this->_summaryInventario->calculateDiff($items, $inDb);
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->user_id(auth()->user()->id)
                ->description('Error al ver diferencias del inventario.')
                ->log();

            return false;
        }
    }

    public function finish()
    {
        try {
            DB::beginTransaction();
            $items = $this->prepareForCalculation();
            $inDb = $this->_productosRepository->all();
            $diff = $this->_summaryInventario->calculateDiff($items, $inDb);

            $this->_summaryInventario->finish($diff);
            $this->setAllProcessed();

            DB::commit();

            $this->_logger
                ->success()
                ->description('Se finalizó el re-inventario correctamente.')
                ->module($this::class)
                ->user_id(auth()->user()->id)
                ->log();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->exception($e)
                ->module($this::class)
                ->user_id(auth()->user()->id)
                ->description('Error al finalizar el re-inventario.')
                ->log();

            return false;
        }
    }




    private function prepareForCalculation()
    {
        $items = $this->getAllUnprocessed();
        return $this->putProductoIdAsKey($items);
    }

    private function getAllUnprocessed()
    {
        $items = Inventario::with('producto')
            ->where('procesado', 0)
            ->get();

        return $items;
    }

    private function putProductoIdAsKey($items)
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item['producto']['id']] = [
                'codigo_barras' => $item['producto']['codigo_barras'],
                'nombre' => $item['producto']['nombre'],
                'descripcion' => $item['producto']['descripcion'],
                'cantidad' => $item['cantidad'],
                'compra' => $item['compra'],
                'venta' => $item['venta'],
            ];
        }

        return $result;
    }
}
