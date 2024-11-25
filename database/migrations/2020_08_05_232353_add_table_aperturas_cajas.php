<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableAperturasCajas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aperturas_caja', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal("monto_inicio");
            $table->decimal("ventas_finales")->nullable();
            $table->decimal("utilidades")->nullable();
            $table->string("observaciones", 255)->default("Ninguna");
            $table->dateTime("fecha_hora_cierre")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aperturas_caja');
    }
}
