<?php

namespace App\Classes\ProductosQuery;

interface IProductosQuery
{
    public function term(string $term): IProductosQuery;
    public function categoria_id(string $categoria_id): IProductosQuery;
    public function caducidad(string $caducidad): IProductosQuery;
    public function compra(array $compra): IProductosQuery;
    public function venta(array $venta): IProductosQuery;
    public function stock(int $stock): IProductosQuery;
    public function tags(array $tags): IProductosQuery;
    public function limit(array $limit): IProductosQuery;
    public function finish(): string;
}
