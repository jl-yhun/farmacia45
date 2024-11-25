<?php

namespace Database\Seeders;

use Database\Seeders\Proveedores\ProveedoresSeeder;
use Database\Seeders\Sucursales\SucursalesSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //  $this->call(VentasUsuariosSeeder::class);
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(AdminAccessSeed::class);
        $this->call(ProveedoresSeeder::class);
        $this->call(SucursalesSeeder::class);
    }
}
