<?php

namespace App\Classes;

use App\Enums\OrdenCompraEstado;
use App\Events\MinStockReached;
use App\Helpers\LoggerBuilder;
use App\Repositories\IProductosRepository;
use App\Repositories\ISimilaresRepository;
use Illuminate\Support\Facades\Auth;

class StockManager implements IStockManager
{

    private $_logger;

    public function __construct(
        LoggerBuilder $logger
    ) {
        $this->_logger = $logger;
    }

    public function wasMinStockExcedeed($producto)
    {
        return $producto->missing_stock > 0;
    }

    public function tryAdjustStockFromOrderProducts($order, IProductosRepository $productosRepository)
    {
        if ($order->estado != OrdenCompraEstado::Aplicado->value)
            return;

        foreach ($order->productos as $product) {
            $cantidad = $product->pivot->cantidad;
            if ($product->isGranel)
                $cantidad = $product->pivot->cantidad * $product->unidades_paquete;
            $productosRepository->modifyStockByAmount($product, $cantidad ?? 0);
        }
    }

    public function tryTriggerMinStockReachedEvent($producto)
    {
        try {
            $wasExcedeed = $this->wasMinStockExcedeed($producto);

            MinStockReached::dispatchIf($wasExcedeed, $producto->id);
        } catch (\Throwable $th) {
            $this->_logger
                ->error()
                ->description('Error al ejecutar evento.')
                ->exception($th)
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->link_id($producto->id)
                ->method('tryTriggerMinStockReachedEvent')
                ->log();
        }
    }

    public function getBestProviderForPurchase($producto)
    {
        try {
            $proveedores = $producto->proveedores->toArray();

            if (count($proveedores) == 0)
                return null;

            if ($this->isTheOnlyOneAvailable($proveedores))
                return null;

            $this->orderProveedores($proveedores);

            return $proveedores[0];
        } catch (\Throwable) {
            return null;
        }
    }

    private function orderProveedores(&$proveedores)
    {
        usort($proveedores, function ($a, $b) {
            return [$b['pivot']['disponible'], $b['pivot']['default'], $a['pivot']['precio']] <=>
                [$a['pivot']['disponible'], $a['pivot']['default'], $b['pivot']['precio']];
        });
    }

    private function isTheOnlyOneAvailable(array $proveedores): bool
    {
        return count($proveedores) == 1 && !$proveedores[0]['pivot']['disponible'];
    }
}
