<?php

namespace Database\Seeders;

use App\Enums\FuenteGasto;
use App\Enums\MetodoPago;
use App\Gasto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GastosSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Gasto::updateOrCreate(
            ['id' => 1],
            [
                'apertura_caja_id' => 1,
                'usuario_id' => 1,
                'monto' => 123,
                'fuente' => FuenteGasto::Caja->value,
                'concepto' => 'Concepto 123'
            ]
        );

        Gasto::updateOrCreate(
            ['id' => 2],
            [
                'apertura_caja_id' => 1,
                'usuario_id' => 1,
                'monto' => 456,
                'fuente' => FuenteGasto::Caja->value,
                'concepto' => 'Concepto 456'
            ]
        );

        Gasto::withTrashed()->updateOrCreate(
            ['id' => 3],
            [
                'apertura_caja_id' => 1,
                'usuario_id' => 1,
                'monto' => 789,
                'fuente' => FuenteGasto::Caja->value,
                'concepto' => 'Concepto 789'
            ]
        );

        Gasto::updateOrCreate(
            ['id' => 4],
            [
                'apertura_caja_id' => 1,
                'usuario_id' => 1,
                'monto' => 10,
                'fuente' => FuenteGasto::MercadoPago->value,
                'concepto' => 'Concepto 10'
            ]
        );
    }
}
