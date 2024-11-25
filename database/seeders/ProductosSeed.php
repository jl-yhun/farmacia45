<?php

namespace Database\Seeders;

use App\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Producto::updateOrCreate(
            ['id' => 1],
            [
                'codigo_barras' => '001',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 001',
                'descripcion' => 'PRODUCTO 001 DESC',
                'caducidad' => '2023/01/31',
                'compra' => 100,
                'venta' => 150,
                'stock' => 100
            ]
        );

        Producto::updateOrCreate(
            ['id' => 2],
            [
                'codigo_barras' => '002',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 002',
                'descripcion' => 'PRODUCTO 002 DESC',
                'caducidad' => '2023/01/31',
                'compra' => 80,
                'venta' => 125,
                'stock' => 200
            ]
        );

        Producto::withTrashed()->updateOrCreate(
            ['id' => 3],
            [
                'deleted_at' => null,
                'codigo_barras' => '003',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 003',
                'descripcion' => 'PRODUCTO 003 DESC',
                'caducidad' => '2023/01/31',
                'compra' => 75,
                'venta' => 100,
                'stock' => 300,
                'min_stock' => 100,
                'max_stock' => 300
            ]
        );

        Producto::updateOrCreate(
            ['id' => 4],
            [
                'codigo_barras' => '004',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 004',
                'descripcion' => 'PRODUCTO 004 DESCRIPCION',
                'caducidad' => '2023/01/31',
                'compra' => 80,
                'venta' => 125,
                'stock' => 4,
                'min_stock' => 4,
                'max_stock' => 5
            ]
        );

        Producto::updateOrCreate(
            ['id' => 5],
            [
                'codigo_barras' => '005',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 005',
                'descripcion' => 'PRODUCTO 005 DESC',
                'caducidad' => '2023/01/31',
                'compra' => 100,
                'venta' => 200,
                'stock' => 0
            ]
        );

        Producto::updateOrCreate(
            ['id' => 6],
            [
                'codigo_barras' => '006',
                'categoria_id' => 1,
                'nombre' => 'PRODUCTO 006',
                'descripcion' => 'PRODUCTO 006 DESC',
                'caducidad' => '2023/01/31',
                'compra' => 100,
                'venta' => 200,
                'stock' => 2,
                'min_stock' => 2,
                'max_stock' => 4,
                'pedidos' => 1
            ]
        );
    }
}
