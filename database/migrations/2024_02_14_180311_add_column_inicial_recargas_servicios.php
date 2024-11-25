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
            $table->decimal('inicial_recargas_servicios', 10, 2)->after('inicial_apartados');
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
            $table->dropColumn('inicial_recargas_servicios');
        });
    }
};
