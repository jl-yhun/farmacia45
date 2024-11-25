<?php

namespace Database\Seeders\Proveedores;

use App\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Proveedor::updateOrCreate([
            'id' => 1
        ], [
            'nombre' => 'Levic',
            'short_name' => 'Levic'
        ]);

        Proveedor::updateOrCreate([
            'id' => 2
        ], [
            'nombre' => 'Quepharma',
            'short_name' => 'Quepharma'
        ]);

        Proveedor::updateOrCreate([
            'id' => 3
        ], [
            'nombre' => 'Nadro',
            'short_name' => 'Nadro'
        ]);

        Proveedor::updateOrCreate([
            'id' => 4
        ], [
            'nombre' => 'Popular Farmaceútico',
            'short_name' => 'Popular'
        ]);

        Proveedor::updateOrCreate([
            'id' => 5
        ], [
            'nombre' => 'Equilibrio',
            'short_name' => 'EQ'
        ]);

        Proveedor::updateOrCreate([
            'id' => 6
        ], [
            'nombre' => 'Marzam',
            'short_name' => 'Marzam'
        ]);

        Proveedor::updateOrCreate([
            'id' => 7
        ], [
            'nombre' => 'Medicine Depot',
            'short_name' => 'Depot'
        ]);

        Proveedor::updateOrCreate([
            'id' => 8
        ], [
            'nombre' => 'Jarritos',
            'short_name' => 'Jarritos'
        ]);

        Proveedor::updateOrCreate([
            'id' => 9
        ], [
            'nombre' => 'Cubrebocas',
            'short_name' => 'Cubrebocas'
        ]);

        Proveedor::updateOrCreate([
            'id' => 10
        ], [
            'nombre' => 'Genomalab',
            'short_name' => 'Genoma'
        ]);

        Proveedor::updateOrCreate([
            'id' => 11
        ], [
            'nombre' => 'Dimen',
            'short_name' => 'Dimen'
        ]);

        Proveedor::updateOrCreate([
            'id' => 12
        ], [
            'nombre' => 'Farmacias Sana Sana',
            'short_name' => 'Sana Sana'
        ]);

        Proveedor::updateOrCreate([
            'id' => 13
        ], [
            'nombre' => 'Farma Center',
            'short_name' => 'Center'
        ]);
    }
}
