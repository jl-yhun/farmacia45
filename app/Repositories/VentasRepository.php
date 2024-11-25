<?php

namespace App\Repositories;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\LoggerBuilder;
use App\Producto;
use App\Venta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class VentasRepository implements IVentasRepository
{
    private $_cierreAperturaCajaBuilder;
    private $_productosRepository;
    private $_descuentosRepository;
    private $_logger;

    public function __construct(
        IAperturasCajaRepository $cierreAperturaCajaBuilder,
        IProductosRepository $productosRepository,
        IDescuentosRepository $descuentosRepository,
        LoggerBuilder $logger
    ) {
        $this->_cierreAperturaCajaBuilder = $cierreAperturaCajaBuilder;
        $this->_productosRepository = $productosRepository;
        $this->_descuentosRepository = $descuentosRepository;
        $this->_logger = $logger;
    }

    public function getLast()
    {
        return Venta::orderBy('id', 'desc')->first();
    }

    public function show($id)
    {
        return Venta::find($id);
    }

    public function create($data)
    {
        try {

            DB::beginTransaction();
            $venta = Venta::create([
                ...$data,
                'denominacion' => $data['se-recibe'] ?? 0,
                'utilidad' => 0,
                'cambio' => $data['metodo_pago'] != 'Efectivo' ? 0 : $data['se-recibe'] - $data['total'],
                'apertura_caja_id' => $this->_cierreAperturaCajaBuilder->getCurrent()->id,
                'usuario_id' => auth()->user()->id,
            ]);

            $this->_linkProductosToVenta($data['productos'], $venta);
            $this->_linkDescuentosToVenta($data['descuentos'] ?? [], $venta);

            $venta->utilidad = $this->_calculateProfit($venta->id);
            $venta->save();

            DB::commit();

            return true;
        } catch (StockLessThanZeroException $e) {
            DB::rollBack();
            session()->flash('flash', [
                'kind' => 'danger',
                'msj' => $e->getMessage()
            ]);

            $this->_logger
                ->error()
                ->description($e->getMessage())
                ->before(json_encode($data))
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->exception($e)
                ->log();
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('flash', [
                'kind' => 'danger',
                'msj' => config('app.fatal')
            ]);

            $this->_logger
                ->error()
                ->description('Error al realizar venta.')
                ->before(json_encode($data))
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->exception($e)
                ->log();
        }
        return false;
    }

    public function delete(Venta $venta)
    {
        try {
            DB::beginTransaction();

            $ventaProductos = DB::select(
                'SELECT producto_id, cantidad FROM ventas_productos WHERE venta_id = :ventaId',
                ['ventaId' => $venta->id]
            );

            foreach ($ventaProductos as $ventaProducto) {
                $producto = $this->_productosRepository->show($ventaProducto->producto_id);
                $this->_returnSoldAmount($producto, $ventaProducto->cantidad);
            }

            $venta->delete();

            DB::commit();

            $this->_logger
                ->success()
                ->description($this::class . '::delete finished')
                ->module($this::class)
                ->link_id($venta->id)
                ->user_id(Auth::user()->id)
                ->log();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->description($this::class . '::delete finished with error')
                ->module($this::class)
                ->link_id($venta->id)
                ->user_id(Auth::user()->id)
                ->exception($th)
                ->log();
        }

        return false;
    }

    public function deleteItem(Venta $venta, Producto $producto)
    {
        try {
            DB::beginTransaction();

            if ($this->_isLastItemInSale($venta)) {
                DB::commit();
                // Remove all the sale
                return $this->delete($venta);
            }

            $ventaProducto = DB::select(
                'SELECT cantidad FROM ventas_productos WHERE venta_id = :ventaId AND producto_id = :productoId',
                [
                    'ventaId' => $venta->id,
                    'productoId' => $producto->id
                ]
            );

            $this->_returnSoldAmount($producto, $ventaProducto[0]->cantidad);

            DB::table('ventas_productos')
                ->where('venta_id', $venta->id)
                ->where('producto_id', $producto->id)
                ->delete();

            DB::table('ventas')
                ->where('id', $venta->id)
                ->update([
                    'total' => $this->_recalculateTotal($venta->id),
                    'utilidad' => $this->_calculateProfit($venta->id)
                ]);

            DB::commit();

            $this->_logger
                ->success()
                ->description($this::class . '::deleteItem finished for venta ' . $venta->id)
                ->module($this::class)
                ->link_id($producto->id)
                ->user_id(Auth::user()->id)
                ->log();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->description($this::class . '::deleteItem finished with error for venta ' . $venta->id)
                ->module($this::class)
                ->link_id($producto->id)
                ->user_id(Auth::user()->id)
                ->exception($th)
                ->log();
        }

        return false;
    }

    public function reportByDateRange(string $startDate = null, string $endDate = null)
    {
        try {
            $resultDates = $this->_formatDateRanges($startDate, $endDate);
            $ventas = DB::select(<<<EOT
                    SELECT v.id, total, denominacion, cambio, utilidad, v.created_at, u.name usuario
                    FROM ventas v
                    INNER JOIN users u ON u.id = v.usuario_id
                    WHERE v.created_at BETWEEN :startDate AND :endDate 
                    AND v.deleted_at IS NULL 
                    ORDER BY v.created_at DESC
                   EOT, [...$resultDates]);

            $resultSet = [];

            foreach ($ventas as $venta) {
                $productos = DB::select(<<<EOT
                SELECT p.id, vp.cantidad, p.nombre, p.descripcion, p.codigo_barras, p.stock, p.min_stock, p.max_stock
                FROM ventas_productos vp
                INNER JOIN productos p ON p.id = vp.producto_id
                WHERE vp.venta_id = :ventaId 
               EOT, ['ventaId' => $venta->id]);

                $resultSet[] = [...get_object_vars($venta), 'productos' => $productos];
            }

            return $resultSet;
        } catch (Throwable $e) {
            $this->_logger
                ->error()
                ->description('Error al obtener lista de ventas')
                ->module($this::class)
                ->user_id(Auth::user()->id)
                ->exception($e)
                ->log();
            throw $e;
        }
    }


    private function _calculateProfit($id)
    {
        try {
            return DB::select(<<<EOT
                SELECT SUM((venta - compra) * cantidad) as profit
                FROM ventas_productos 
                WHERE venta_id = :ventaId
                EOT, ['ventaId' => $id])[0]->profit;
        } catch (Exception $e) {
            $this->_logger
                ->error()
                ->description('Error al calcular profit')
                ->module($this::class)
                ->link_id($id)
                ->user_id(auth()->user()->id)
                ->exception($e)
                ->log();
            return 0;
        }
    }

    private function _formatDateRanges(string $startDate = null, string $endDate = null): array
    {
        $newStartDate = ($startDate && $endDate) ? $startDate : date('Y-m-d');
        $newEndDate = ($startDate && $endDate) ? $endDate : date('Y-m-d');
        return ['startDate' => $newStartDate . ' 00:00:00', 'endDate' => $newEndDate . ' 23:59:59'];
    }

    private function _isLastItemInSale(Venta $venta): bool
    {
        $numVentasProductos = DB::select(
            'SELECT COUNT(1) valor FROM ventas_productos WHERE venta_id = :ventaId',
            ['ventaId' => $venta->id]
        );

        return $numVentasProductos[0]->valor <= 1;
    }

    private function _linkProductosToVenta($productos, $venta)
    {
        foreach ($productos as $prod) {
            $producto = $this->_productosRepository->show($prod['id']);
            $this->_productosRepository->modifyStockByAmount($producto, -$prod['cantidad']);

            $venta->productos()->attach($prod['id'], [
                'cantidad' => $prod['cantidad'],
                'venta' => $prod['venta'],
                'compra' => $producto->compra
            ]);
        }
    }

    private function _linkDescuentosToVenta($descuentos, $venta)
    {
        foreach ($descuentos as $descuento) {
            $descuento = $this->_descuentosRepository->create($descuento);
            $venta->descuentos()->attach($descuento->id);
        }
    }

    private function _recalculateTotal(int $venta_id)
    {
        return DB::select('SELECT SUM(cantidad * venta) total FROM ventas_productos WHERE venta_id = :ventaId', [
            'ventaId' => $venta_id
        ])[0]->total;
    }

    private function _returnSoldAmount(Producto $producto, int $cantidad)
    {
        $this->_productosRepository->modifyStockByAmount($producto, $cantidad);
    }
}
