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
        Schema::table('productos', function (Blueprint $table) {
            $table->text('descripcion')->after('nombre');
            $table->date('caducidad')->after('descripcion')->nullable();
            $table->string('codigo_barras', 100)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->dropColumn('caducidad');
            $table->dropColumn('codigo_barras');
        });
    }
};
