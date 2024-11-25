<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Dbv16 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE garantias MODIFY tipo ENUM('GARANTÍA', 'CAMBIO', 'CANCELACIÓN', 'DEVOLUCIÓN DE DINERO')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE garantias MODIFY tipo ENUM('GARANTÍA', 'CAMBIO', 'DEVOLUCIÓN DE DINERO')");
        // Schema::table('garantias', function (Blueprint $table) {
        //     $table->enum("tipo", ["GARANTÍA", "CAMBIO","DEVOLUCIÓN DE DINERO"])->change();
        // });
    }
}
