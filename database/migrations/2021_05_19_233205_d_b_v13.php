<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV13 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("apertura_caja_id");
            $table->bigInteger("usuario_id");
            $table->decimal("monto", 10, 2);
            $table->string("concepto", 100);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table("aperturas_caja", function (Blueprint $table) {
            $table->decimal("gastos", 10, 2)->after("electronico")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gastos');
        Schema::table("aperturas_caja", function (Blueprint $table) {
            $table->dropColumn("gastos");
        });
    }
}
