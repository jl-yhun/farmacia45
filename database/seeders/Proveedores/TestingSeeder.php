<?php

namespace Database\Seeders\Proveedores;

use Database\Seeders\AdminAccessSeed;
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
        DB::table('proveedores')->truncate();
        DB::table('productos_proveedores')->truncate();
        
        $this->call(AdminAccessSeed::class);
        $this->call(ProveedoresSeeder::class);
    }
}
