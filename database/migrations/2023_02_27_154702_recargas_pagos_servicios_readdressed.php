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
        Schema::table('recargas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta de débito', 'Tarjeta de crédito'])
                ->after('compania')->default('Efectivo');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta de débito', 'Tarjeta de crédito'])
                ->after('servicio')->default('Efectivo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recargas', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};
