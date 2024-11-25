<?php

namespace App\Repositories;

use App\Producto;
use App\Venta;

interface IVentasRepository
{
    public function create($data);
    public function show($id);
    public function getLast();
    public function delete(Venta $venta);
    public function deleteItem(Venta $venta, Producto $producto);
    public function reportByDateRange(string $startDate, string $endDate);
}
