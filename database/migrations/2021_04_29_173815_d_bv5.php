<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBv5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->enum("tipo", ["GARANTÍA", "CAMBIO", "DEVOLUCIÓN DE DINERO"])->after("motivo_id");
        });
        Schema::table('categorias', function (Blueprint $table) {
            $table->enum("admite", ["GARANTÍA", "CAMBIO", "NINGUNO"])->after("nombre")->default("GARANTÍA");
            $table->dropColumn("permite_cambio");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->dropColumn("tipo");
        });
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn("admite");
            $table->tinyInteger("permite_cambio")->default(0)->after("nombre");
        });
    }
}
