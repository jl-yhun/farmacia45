<?php

namespace Database\Seeders\Similares;

use Database\Seeders\AdminAccessSeed;
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
        $this->call(AdminAccessSeed::class);
        $this->call(ProductosSeed::class);
        $this->call(UsuariosSeed::class);

        DB::table('productos_similares')->truncate();

        $this->seedSimilares();
    }

    private function seedSimilares()
    {
        DB::table('productos_similares')->insert([
            'base_producto_id' => 1,
            'similar_producto_id' => 2
        ]);

        DB::table('productos_similares')->insert([
            'base_producto_id' => 2,
            'similar_producto_id' => 1
        ]);

        DB::table('productos_similares')->insert([
            'base_producto_id' => 1,
            'similar_producto_id' => 4
        ]);

        DB::table('productos_similares')->insert([
            'base_producto_id' => 4,
            'similar_producto_id' => 1
        ]);
    }
}
