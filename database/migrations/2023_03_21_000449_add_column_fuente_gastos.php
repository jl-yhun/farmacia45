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
        Schema::table('gastos', function (Blueprint $table) {
            $table->enum('fuente', ['Caja', 'Apartados', 'Mercado Pago'])
                ->default('Caja')
                ->after('id');
            $table->dropColumn('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gastos', function (Blueprint $table) {
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta de débito', 'Tarjeta de crédito'])
                ->after('id')
                ->default('Efectivo');
            $table->dropColumn('fuente');
        });
    }
};
