<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->decimal('subtotal', 10, 2)->default(0)->after('ventas_finales');
            $table->decimal('en_caja', 10, 2)->default(0)->after('subtotal');
        });

        DB::statement('UPDATE aperturas_caja SET subtotal = ventas_finales+diferencia_garantias-devoluciones-gastos,
            en_caja=ventas_finales+diferencia_garantias-devoluciones-gastos+monto_inicio');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aperturas_caja', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->dropColumn('en_caja');
        });
    }
};
