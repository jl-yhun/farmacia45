<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('CREATE PROCEDURE `sp_get_profit`(IN venta_id INT)
        BEGIN
            SELECT SUM((vp.venta - p.compra) * vp.cantidad) as profit
              FROM ventas v 
        INNER JOIN ventas_productos vp ON v.id = vp.venta_id
        INNER JOIN productos p ON vp.producto_id = p.id
             WHERE v.id = venta_id;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_profit');
    }
};
