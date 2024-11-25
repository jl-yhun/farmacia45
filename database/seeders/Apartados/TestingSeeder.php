<?php

namespace Database\Seeders\Apartados;

use App\Apartado;
use Database\Seeders\AddConfiguracionesGeneralesSeeder;
use Database\Seeders\AdminAccessSeed;
use Database\Seeders\UsuariosSeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        DB::table('apartados')->truncate();
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);

        $this->seedApartados();
    }

    private function seedApartados()
    {
        Apartado::create([
            'monto' => 400,
            'concepto' => 'RENTA, INTERNET Y SUELDOS',
            'usuario_id' => 1,
            'apertura_caja_id' => 1
        ]);

        Apartado::create([
            'monto' => 400,
            'concepto' => 'RENTA, INTERNET Y SUELDOS',
            'usuario_id' => 1,
            'apertura_caja_id' => 2
        ]);

        Apartado::create([
            'monto' => 400,
            'concepto' => 'RENTA, INTERNET Y SUELDOS',
            'usuario_id' => 2,
            'apertura_caja_id' => 3,
            'created_at' => null
        ]);
    }
}
