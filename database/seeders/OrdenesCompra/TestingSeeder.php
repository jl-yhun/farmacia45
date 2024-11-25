<?php

namespace Database\Seeders\OrdenesCompra;

use App\Enums\OrdenCompraEstado;
use App\OrdenCompra;
use App\Producto;
use Database\Seeders\AdminAccessSeed;
use Database\Seeders\ProductosSeed;
use Database\Seeders\Proveedores\ProveedoresSeeder;
use Database\Seeders\UsuariosSeed;
use Database\Seeders\VentasSeed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestingSeeder extends Seeder
{
    public function run()
    {
        DB::table('ordenes_compra')->truncate();
        DB::table('ordenes_compra_productos')->truncate();
        DB::table('ventas_productos')->truncate();
        DB::table('ventas')->truncate();
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);
        $this->call(ProveedoresSeeder::class);
        $this->call(ProductosSeed::class);
        $this->call(VentasSeed::class);
        $this->seedOrdenesCompra();
    }

    private function seedOrdenesCompra()
    {
        $producto1 = Producto::find(1);
        $producto2 = Producto::find(2);
        $producto3 = Producto::find(3);
        $producto4 = Producto::find(4);
        $producto5 = Producto::find(5);

        $ordenCompra1 = OrdenCompra::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'estado' => OrdenCompraEstado::Pendiente->value,
                'proveedor_id' => 1,
                'creador_id' => 1,
                'recibidor_id' => 1,
                'aplicador_id' => 1,
                'apertura_caja_id' => 1,
                'total' => 260
            ]
        );

        $ordenCompra1->productos()->attach($producto4->id, [
            'cantidad' => 2,
            'compra' => $producto4->compra
        ]);

        $ordenCompra1->productos()->attach($producto5->id, [
            'cantidad' => 1,
            'compra' => $producto5->compra
        ]);



        $ordenCompra2 = OrdenCompra::updateOrCreate(
            [
                'id' => 2
            ],
            [
                'estado' => OrdenCompraEstado::Recibido->value,
                'proveedor_id' => 2,
                'creador_id' => 1,
                'recibidor_id' => 1,
                'aplicador_id' => 1,
                'apertura_caja_id' => 1,
                'total' => 525
            ]
        );

        $ordenCompra2->productos()->attach($producto1->id, [
            'cantidad' => 3,
            'compra' => $producto1->compra
        ]);

        $ordenCompra2->productos()->attach($producto3->id, [
            'cantidad' => 3,
            'compra' => $producto3->compra
        ]);


        $ordenCompra3 = OrdenCompra::updateOrCreate(
            [
                'id' => 3
            ],
            [
                'estado' => OrdenCompraEstado::Aplicado->value,
                'proveedor_id' => 1,
                'creador_id' => 1,
                'recibidor_id' => 1,
                'aplicador_id' => 1,
                'apertura_caja_id' => 1,
                'total' => 680
            ]
        );

        $ordenCompra3->productos()->attach($producto4->id, [
            'cantidad' => 1,
            'compra' => $producto4->compra
        ]);

        $ordenCompra3->productos()->attach($producto2->id, [
            'cantidad' => 5,
            'compra' => $producto2->compra
        ]);

        $ordenCompra3->productos()->attach($producto5->id, [
            'cantidad' => 2,
            'compra' => $producto5->compra
        ]);



        $ordenCompra4 = OrdenCompra::updateOrCreate(
            [
                'id' => 4
            ],
            [
                'estado' => OrdenCompraEstado::Pedido->value,
                'proveedor_id' => 1,
                'creador_id' => 1,
                'recibidor_id' => 1,
                'aplicador_id' => 1,
                'apertura_caja_id' => 1,
                'total' => 500
            ]
        );

        $ordenCompra4->productos()->attach($producto1->id, [
            'cantidad' => 5,
            'compra' => $producto1->compra
        ]);
    }
}
