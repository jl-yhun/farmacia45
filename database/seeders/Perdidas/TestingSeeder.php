<?php

namespace Database\Seeders\Perdidas;

use Database\Seeders\AddConfiguracionesGeneralesSeeder;
use Database\Seeders\AdminAccessSeed;
use Database\Seeders\PerdidasSeed;
use Database\Seeders\ProductosSeed;
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
        DB::table('perdidas')->truncate();
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);
        $this->call(ProductosSeed::class);
        $this->call(PerdidasSeed::class);
    }
}
