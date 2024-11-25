<?php

namespace Database\Seeders\Usuarios;

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
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);
    }
}
