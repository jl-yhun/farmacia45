<?php

namespace Database\Seeders\Categorias;

use Database\Seeders\AdminAccessSeed;
use Database\Seeders\CategoriasSeed;
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
        DB::table('categorias')->truncate();
        
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);
        $this->call(CategoriasSeed::class);
    }
}
