<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV14 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("usuario_id")->index();
            $table->bigInteger("producto_id")->index();
            $table->decimal("descuento", 10, 2);
            $table->decimal("nuevo", 10, 2);
            $table->enum("tipo", ["monto", "porcentaje"]);
            $table->string("motivo", 255);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('ventas_descuentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("venta_id")->index();
            $table->integer("descuento_id")->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descuentos');
        Schema::dropIfExists('ventas_descuentos');
    }
}
