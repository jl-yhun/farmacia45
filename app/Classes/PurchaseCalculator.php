<?php

namespace App\Classes;

use App\Producto;

class PurchaseCalculator
{
    public static function calculateAmountForPurchase(Producto $producto)
    {
        if ($producto->isGranel)
            return ceil(($producto->max_stock - $producto->min_stock + 1) / $producto->unidades_paquete);
        return $producto->best_stock;
    }
}
