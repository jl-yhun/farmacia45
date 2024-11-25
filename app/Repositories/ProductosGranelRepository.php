<?php

namespace App\Repositories;

use App\Classes\IStockManager;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\LoggerBuilder;
use App\Helpers\SanitizerBuilder;
use App\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductosGranelRepository implements IProductosGranelRepository
{
    private $_logger;

    public function __construct(
        LoggerBuilder $logger
    ) {
        $this->_logger = $logger;
    }

    public function tryCreateProductoGranel($producto, $input)
    {
        if(!isset($input['isGranel']))
            return;

        if (!filter_var($input['isGranel'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $this->tryRemoveProductoGranel($producto);
            return;
        }
        $productoGranel = [
            'unidades_paquete' => $input['unidades_paquete']
        ];

        $producto->producto_granel()->updateOrCreate([
            'producto_id' => $producto->id
        ], $productoGranel);
    }

    private function tryRemoveProductoGranel($producto)
    {
        if ($producto->producto_granel)
            $producto->producto_granel()->delete();
        $producto->save();
    }
}
