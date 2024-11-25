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
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->enum('estado', ['Pendiente', 'Pedido', 'Recibido', 'Aplicado'])->default('Pendiente');
            $table->integer('proveedor_id');
            $table->integer('creador_id');
            $table->integer('recibidor_id');
            $table->integer('aplicador_id');
            $table->integer('apertura_caja_id');
            $table->integer('total');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ordenes_compra_productos', function (Blueprint $table) {
            $table->integer('orden_compra_id');
            $table->integer('producto_id');
            $table->integer('cantidad');
            $table->decimal('compra', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_compra');
        Schema::dropIfExists('ordenes_compra_productos');
    }
};
