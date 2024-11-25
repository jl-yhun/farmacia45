<?php

namespace Database\Seeders;

use App\Inventario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioControllerTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('productos')->truncate();
        DB::table('inventarios')->truncate();
        DB::table('log')->truncate();

        $this->call(AdminAccessSeed::class);
        $this->call(ProductosSeed::class);
        $this->seedInventarios();
    }

    private function seedInventarios()
    {
        Inventario::updateOrCreate(
            [
                'producto_id' => 1,
            ],
            [
                'cantidad' => 100,
                'compra' => 100,
                'venta' => 160
            ]
        );

        Inventario::updateOrCreate(
            [
                'producto_id' => 2,
            ],
            [
                'cantidad' => 200,
                'compra' => 80,
                'venta' => 125
            ]
        );

        Inventario::updateOrCreate(
            [
                'producto_id' => 4,
            ],
            [
                'cantidad' => 8,
                'compra' => 80,
                'venta' => 125
            ]
        );

        Inventario::updateOrCreate(
            [
                'producto_id' => 5,
            ],
            [
                'cantidad' => 1,
                'compra' => 100,
                'venta' => 200
            ]
        );
    }
}
