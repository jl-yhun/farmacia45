<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DBV4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("producto_id");
            $table->bigInteger("venta_id");
            $table->bigInteger("motivo_id");
            $table->text("observaciones")->nullable();
            $table->decimal("diferencia", 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('motivos_garantias', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 50)->unique();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('garantias_productos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("garantia_id");
            $table->bigInteger("producto_id");
        });
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 100)->unique();
            $table->tinyInteger("permite_cambio")->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table("productos", function (Blueprint $table) {
            $table->bigInteger("categoria_id")->after("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garantias');
        Schema::dropIfExists('motivos_garantias');
        Schema::dropIfExists('garantias_productos');
        Schema::dropIfExists('categorias');
        Schema::table("productos", function (Blueprint $table) {
            $table->dropColumn("categoria_id");
        });
    }
}
