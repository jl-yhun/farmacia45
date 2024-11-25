<?php

namespace Database\Seeders;

use App\Perdida;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerdidasSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Perdida::updateOrCreate(
            ['id' => 1],
            [
                'producto_id' => 1,
                'usuario_id' => 1,
                'garantia_id' => 1,
                'motivo' => 'Motivo 123'
            ]
        );

        Perdida::updateOrCreate(
            ['id' => 2],
            [
                'producto_id' => 1,
                'usuario_id' => 1,
                'garantia_id' => 1,
                'motivo' => 'Motivo 456'
            ]
        );

        Perdida::withTrashed()->updateOrCreate(
            ['id' => 3],
            [
                'producto_id' => 1,
                'usuario_id' => 1,
                'garantia_id' => 1,
                'motivo' => 'Motivo 789'
            ]
        );
    }
}
