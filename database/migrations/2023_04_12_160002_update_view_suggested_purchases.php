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
        DB::statement("DROP VIEW view_suggested_purchase");

        DB::statement('CREATE VIEW view_suggested_purchase AS
        select p.id,p.codigo_barras, 
        p.nombre, p.descripcion, p.stock,vp.cantidad sugerido,
        v.apertura_caja_id, v.created_at, (SELECT COUNT(*) 
       		FROM productos_similares ps WHERE ps.base_producto_id = p.id) similares
        from ventas v
        INNER JOIN ventas_productos vp ON v.id = vp.venta_id
        INNER JOIN productos p ON vp.producto_id = p.id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW view_suggested_purchase");
        DB::statement('CREATE VIEW view_suggested_purchase AS
        select p.codigo_barras, 
        p.nombre, p.descripcion, p.stock,vp.cantidad sugerido,
        v.apertura_caja_id, v.created_at
        from ventas v
        INNER JOIN ventas_productos vp ON v.id = vp.venta_id
        INNER JOIN productos p ON vp.producto_id = p.id');
    }
};
