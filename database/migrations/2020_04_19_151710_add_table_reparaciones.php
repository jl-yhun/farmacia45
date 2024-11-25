<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableReparaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string("folio", 100)->unique();
            $table->integer("usuario_id")->index();
            $table->string("marca", 100);
            $table->string("modelo", 100);
            $table->string("cliente", 100);
            $table->string("telefono")->nullable();
            $table->text("falla");
            $table->decimal("costo", 10, 2);
            $table->decimal("abono", 10, 2);
            $table->date("fecha_entrega");
            $table->text("observaciones");
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
        Schema::dropIfExists('reparaciones');
    }
}
