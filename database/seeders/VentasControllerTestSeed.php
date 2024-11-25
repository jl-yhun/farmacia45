<?php

namespace Database\Seeders;

use Database\Seeders\OrdenesCompra\TestingSeeder;
use Illuminate\Database\Seeder;

class VentasControllerTestSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TruncateNeededTables::class);
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(TestingSeeder::class);
    }
}
