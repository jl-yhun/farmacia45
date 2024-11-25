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
        Schema::table('aperturas_caja', function (Blueprint $table) {
            $table->decimal('apartados_dia', 10, 2)->default(0)->after('ventas_ele');
            $table->decimal('gastos_apartados', 10, 2)->default(0)->after('gastos_ele');
            $table->decimal('gastos_recargas_servicios', 10, 2)->default(0)->after('gastos_apartados');
            $table->decimal('subtotal_recargas_servicios', 10, 2)->default(0)->after('subtotal_apartados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aperturas_caja', function (Blueprint $table) {
            $table->dropColumn('apartados_dia');
            $table->dropColumn('gastos_apartados');
            $table->dropColumn('gastos_recargas_servicios');
            $table->dropColumn('subtotal_recargas_servicios');
        });
    }
};
