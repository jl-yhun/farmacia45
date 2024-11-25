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
            $table->integer('apertura_caja_id')->after('id');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->integer('apertura_caja_id')->after('id');
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
            $table->dropColumn('apertura_caja_id');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('apertura_caja_id');
        });
    }
};
