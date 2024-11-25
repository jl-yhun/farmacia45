<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoAperturasCaja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aperturas_caja', function (Blueprint $table) {
            $table->enum("estado", ["Pendiente", "Concluido"])->default("Pendiente")->after("fecha_hora_cierre");
            $table->decimal("efectivo", 10, 2)->after("utilidades")->default(0);
            $table->decimal("electronico", 10, 2)->after("efectivo")->default(0);
            $table->decimal("diferencia_garantias", 10, 2)->after("utilidades")->default(0);
            $table->decimal("devoluciones", 10, 2)->after("diferencia_garantias")->default(0);

            $table->decimal("ventas_finales", 10, 2)->nullable(false)->default(0)->change();
            $table->decimal("utilidades", 10, 2)->nullable(false)->default(0)->change();
        });
        Schema::table('ventas', function (Blueprint $table) {
            $table->bigInteger("apertura_caja_id")->after("usuario_id");
        });
        Schema::table('garantias', function (Blueprint $table) {
            $table->bigInteger("apertura_caja_id")->after("usuario_id");
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
            $table->dropColumn("estado");
            $table->dropColumn("diferencia_garantias");
            $table->dropColumn("devoluciones");
            $table->dropColumn("electronico");
            $table->dropColumn("efectivo");

            $table->decimal("ventas_finales", 10, 2)->nullable()->change();
            $table->decimal("utilidades", 10, 2)->nullable()->change();
        });
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn("apertura_caja_id");
        });
        Schema::table('garantias', function (Blueprint $table) {
            $table->dropColumn("apertura_caja_id");
        });
    }
}
