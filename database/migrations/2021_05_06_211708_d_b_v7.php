<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV7 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permisos_temp', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("usuario_id");
            $table->bigInteger("permiso_id");
            $table->dateTime("expiracion");
            $table->string("razon", 255)->default("NO PROPORCIONADA");
            $table->tinyInteger("expiro")->default(0);
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
        Schema::dropIfExists('permisos_temp');
    }
}
