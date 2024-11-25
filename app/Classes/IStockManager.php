<?php

namespace App\Classes;

use App\Repositories\IProductosRepository;

interface IStockManager
{

    public function wasMinStockExcedeed($producto_id);
    public function tryAdjustStockFromOrderProducts($order, IProductosRepository $productosRepository);
    public function tryTriggerMinStockReachedEvent($producto);
    public function getBestProviderForPurchase($producto);
}
