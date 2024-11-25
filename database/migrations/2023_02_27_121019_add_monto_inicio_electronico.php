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
            $table->renameColumn('monto_inicio', 'inicial_efe');
            $table->renameColumn('en_caja', 'subtotal_efe');
            $table->renameColumn('efectivo', 'ventas_efe');
            $table->renameColumn('electronico', 'ventas_ele');
            $table->renameColumn('gastos', 'gastos_efe');
            $table->renameColumn('subtotal', 'total');
            $table->dropColumn('diferencia_garantias');
            $table->dropColumn('devoluciones');
            $table->dropColumn('reparaciones_finales');

            $table->decimal('inicial_ele', 10, 2)
                ->after('monto_inicio')->default(0);

            $table->decimal('gastos_ele', 10, 2)
                ->after('gastos')->default(0);

            $table->decimal('transferencias', 10, 2)
                ->after('gastos_ele')->default(0);

            $table->decimal('servicios_recargas_efe', 10, 2)
                ->after('transferencias')->default(0);

            $table->decimal('servicios_recargas_ele', 10, 2)
                ->after('servicios_recargas_efe')->default(0);

            $table->decimal('comisiones_efe', 10, 2)
                ->after('servicios_recargas_ele')->default(0);

            $table->decimal('comisiones_ele', 10, 2)
                ->after('comisiones_efe')->default(0);

            $table->decimal('subtotal_ele', 10, 2)
                ->after('en_caja')->default(0);
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
            $table->dropColumn('inicial_ele');
            $table->dropColumn('gastos_ele');
            $table->dropColumn('transferencias');
            $table->dropColumn('servicios_recargas_efe');
            $table->dropColumn('servicios_recargas_ele');
            $table->dropColumn('comisiones_efe');
            $table->dropColumn('comisiones_ele');
            $table->dropColumn('subtotal_ele');

            $table->renameColumn('inicial_efe', 'monto_inicio');
            $table->renameColumn('subtotal_efe', 'en_caja');
            $table->renameColumn('ventas_efe', 'efectivo');
            $table->renameColumn('ventas_ele', 'electronico');
            $table->renameColumn('gastos_efe', 'gastos');
            $table->renameColumn('total', 'subtotal');

            $table->decimal('diferencia_garantias', 10, 2)->default(0);
            $table->decimal('devoluciones', 10, 2)->default(0);
            $table->decimal('reparaciones_finales', 10, 2)->default(0);
        });
    }
};
