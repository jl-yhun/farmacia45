<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV8 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('garantias', function (Blueprint $table) {
            $table->tinyInteger("perdida")->default(0)->after("diferencia");
            $table->bigInteger("usuario_id")->after("id");
            $table->string("observaciones", 255)->default("NINGUNA")->change();
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
            $table->dropColumn("perdida");
            $table->dropColumn("usuario_id");
            $table->text("observaciones")->nullable()->change();
        });
    }
}
