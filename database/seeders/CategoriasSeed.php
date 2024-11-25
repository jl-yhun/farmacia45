<?php

namespace Database\Seeders;

use App\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Categoria::updateOrCreate(
            ['id' => 1],
            [
                'nombre' => 'Categoría xyz',
                'admite' => 'NINGUNO',
                'tasa_iva' => .16
            ]
        );

        Categoria::updateOrCreate(
            ['id'     => 2],
            [
                'nombre' => 'Categoría abc',
                'admite' => 'NINGUNO',
                'tasa_iva' => 0
            ]
        );

        Categoria::withTrashed()->updateOrCreate(
            ['id'     => 3],
            [
                'deleted_at' => null,
                'nombre' => 'Categoría abc-d',
                'admite' => 'NINGUNO',
                'tasa_iva' => .16
            ]
        );
    }
}
