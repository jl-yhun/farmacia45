<?php

namespace App\Classes\ProductosQuery;

use Symfony\Component\HttpFoundation\InputBag;

class ProductosQueryBuilder
{
    public static function buildFromQueryParams(InputBag $queryParams): ProductosQuery
    {
        $productosQueryObj = new ProductosQuery();

        foreach ($queryParams as $key => $value) {
            $productosQueryObj->$key($value);
        }

        return $productosQueryObj;
    }
}
