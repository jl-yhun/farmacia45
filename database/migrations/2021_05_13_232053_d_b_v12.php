<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV12 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->index("nombre");
            $table->index("categoria_id");
        });
        Schema::table('ventas_productos', function (Blueprint $table) {
            $table->bigInteger("producto_id")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex("productos_nombre_index");
            $table->dropIndex("productos_categoria_id_index");
        });
        Schema::table('ventas_productos', function (Blueprint $table) {
            $table->integer("producto_id")->change();
        });
    }
}
