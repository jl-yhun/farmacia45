<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateNeededTables extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('aperturas_caja')->truncate();
        DB::table('ventas_productos')->truncate();
        DB::table('transferencias')->truncate();
        DB::table('ventas')->truncate();
        DB::table('descuentos')->truncate();
        DB::table('recargas')->truncate();
        DB::table('servicios')->truncate();
        DB::table('gastos')->truncate();
        DB::table('log')->truncate();
    }
}
