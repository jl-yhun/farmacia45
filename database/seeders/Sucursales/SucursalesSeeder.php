<?php

namespace Database\Seeders\Sucursales;

use App\Sucursal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sucursal::updateOrCreate([
            'id' => 1
        ], [
            'nombre' => 'Farmacia el 45',
            'direccion' => 'Reyes Acozac',
            'host' => 'https://ptv.farmaciael45.online'
        ]);

        Sucursal::updateOrCreate([
            'id' => 2
        ], [
            'nombre' => 'Farmacia el 45 II',
            'direccion' => 'Loma Bonita',
            'host' => 'https://2ptv.farmaciael45.online'
        ]);
    }
}
