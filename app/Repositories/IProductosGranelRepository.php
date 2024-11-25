<?php

namespace App\Repositories;

interface IProductosGranelRepository
{
    public function tryCreateProductoGranel($producto, $input);
}
