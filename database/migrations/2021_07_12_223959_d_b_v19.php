<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV19 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("modulo", 50);
            $table->bigInteger("usuario_id");
            $table->bigInteger("link_id");
            $table->text("descripcion");
            $table->text("antes")->nullable();
            $table->text("despues")->nullable();
            $table->string("tipo", 50);
            $table->text("excepcion");
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
        Schema::dropIfExists('log');
    }
}
