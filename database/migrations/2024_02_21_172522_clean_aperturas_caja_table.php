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
            $table->dropColumn('ventas_finales');
            $table->dropColumn('transferencias');
            $table->dropColumn('comisiones_efe');
            $table->dropColumn('comisiones_ele');
        });

        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_efe DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER servicios_recargas_ele");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_ele DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_efe");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_apartados DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_ele");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_recargas_servicios DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_apartados");
        DB::statement("ALTER TABLE aperturas_caja MODIFY total DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_recargas_servicios");
        DB::statement("ALTER TABLE aperturas_caja MODIFY utilidades DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aperturas_caja', function (Blueprint $table) {
            $table->decimal('ventas_finales', 10, 2)->default(0)->after('inicial_apartados');
            $table->decimal('transferencias', 10, 2)->default(0)->after('gastos_ele');
            $table->decimal('comisiones_efe', 10, 2)->default(0)->after('servicios_recargas_ele');
            $table->decimal('comisiones_ele', 10, 2)->default(0)->after('comisiones_efe');
        });

        DB::statement("ALTER TABLE aperturas_caja MODIFY total DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER ventas_finales");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_efe DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER total");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_ele DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_efe");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_apartados DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_ele");
        DB::statement("ALTER TABLE aperturas_caja MODIFY subtotal_recargas_servicios DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_apartados");
        DB::statement("ALTER TABLE aperturas_caja MODIFY utilidades DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER subtotal_recargas_servicios");
    }
};
