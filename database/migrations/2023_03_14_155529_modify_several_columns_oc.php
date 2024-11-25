<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->integer('recibidor_id')->nullable(true)->change();
            $table->integer('aplicador_id')->nullable(true)->change();
            $table->integer('apertura_caja_id')->nullable(true)->change();
            $table->decimal('total')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->integer('recibidor_id')->nullable(false)->change();
            $table->integer('aplicador_id')->nullable(false)->change();
            $table->integer('apertura_caja_id')->nullable(false)->change();
            $table->integer('total')->change();
        });
    }
};
