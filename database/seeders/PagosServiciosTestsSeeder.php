<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagosServiciosTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(AdminAccessSeed::class);
        DB::table('aperturas_caja')->truncate();
    }
}
