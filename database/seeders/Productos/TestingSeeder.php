<?php

namespace Database\Seeders\Productos;

use App\Producto;
use Database\Seeders\AdminAccessSeed;
use Database\Seeders\CategoriasSeed;
use Database\Seeders\ProductosSeed;
use Database\Seeders\Proveedores\ProveedoresSeeder;
use Database\Seeders\UsuariosSeed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('log')->truncate();
        DB::table('productos_proveedores')->truncate();
        DB::table('productos_granel')->truncate();

        $this->call(AdminAccessSeed::class);
        $this->call(CategoriasSeed::class);
        $this->call(ProductosSeed::class);
        $this->call(UsuariosSeed::class);
        $this->call(ProveedoresSeeder::class);

        $this->seedProductosProveedores();
        $this->seedProductosGranel();
    }

    private function seedProductosProveedores()
    {
        Producto::find(1)->proveedores()->attach(1, [
            'codigo' => 'Levis Code 1',
            'precio' => 23
        ]);

        Producto::find(1)->proveedores()->attach(2, [
            'codigo' => 'Quepharma Code',
            'precio' => 25,
            'default' => true
        ]);
    }

    private function seedProductosGranel()
    {
        Producto::find(3)->producto_granel()->create([
            'unidades_paquete' => 2
        ]);
    }
}
