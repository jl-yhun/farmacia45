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
        select * from (
            select p.id,p.codigo_barras, 
                    p.nombre, p.descripcion, p.stock, p.min_stock,vp.cantidad sugerido,
                    v.apertura_caja_id, v.created_at, (SELECT COUNT(*) 
                            FROM productos_similares ps WHERE ps.base_producto_id = p.id) similares,
                            (select COUNT(*) from productos_proveedores pp where pp.producto_id = p.id and pp.disponible = 1) proveedores,
                            IFNULL((select SUM(ocp.cantidad) from ordenes_compra_productos ocp 
                                INNER JOIN ordenes_compra oc ON ocp.orden_compra_id = oc.id 
                                where ocp.producto_id = p.id and oc.estado NOT IN(\'Recibido\', \'Aplicado\')), 0) pedidos
                    from ventas v
                    INNER JOIN ventas_productos vp ON v.id = vp.venta_id
                    INNER JOIN productos p ON vp.producto_id = p.id) f
            WHERE f.min_stock >= (f.stock + f.pedidos)');
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
        select * from (
            select p.id,p.codigo_barras, 
                    p.nombre, p.descripcion, p.stock, p.min_stock,vp.cantidad sugerido,
                    v.apertura_caja_id, v.created_at, (SELECT COUNT(*) 
                            FROM productos_similares ps WHERE ps.base_producto_id = p.id) similares,
                            (select COUNT(*) from productos_proveedores pp where pp.producto_id = p.id and pp.disponible = 1) proveedores,
                            IFNULL((select SUM(ocp.cantidad) from ordenes_compra_productos ocp 
                                INNER JOIN ordenes_compra oc ON ocp.orden_compra_id = oc.id 
                                where ocp.producto_id = p.id and oc.estado <> \'Recibido\'), 0) pedidos
                    from ventas v
                    INNER JOIN ventas_productos vp ON v.id = vp.venta_id
                    INNER JOIN productos p ON vp.producto_id = p.id) f
            WHERE f.min_stock >= (f.stock + f.pedidos)');
    }
};
