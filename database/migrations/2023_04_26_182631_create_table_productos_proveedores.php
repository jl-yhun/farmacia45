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
        Schema::create('productos_proveedores', function (Blueprint $table) {
            $table->integer('producto_id')->index();
            $table->integer('proveedor_id')->index();
            $table->string('codigo', 50);
            $table->boolean('disponible')->default(true);
            $table->timestamp('last_check')->useCurrent();

            $table->unique(['producto_id', 'proveedor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_proveedores');
    }
};
