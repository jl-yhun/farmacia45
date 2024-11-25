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
        Schema::table('productos_proveedores', function (Blueprint $table) {
            $table->dropUnique(['producto_id', 'proveedor_id', 'codigo']);
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
        Schema::table('productos_proveedores', function (Blueprint $table) {
            $table->dropUnique(['producto_id', 'proveedor_id']);
            $table->unique(['producto_id', 'proveedor_id', 'codigo']);
        });
    }
};
