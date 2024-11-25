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
            $table->decimal('inicial_apartados', 10, 2)->nullable(false)->after('inicial_ele');
            $table->decimal('subtotal_apartados', 10, 2)->nullable(false)->default(0)->after('subtotal_ele');
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
            $table->dropColumn('inicial_apartados');
            $table->dropColumn('subtotal_apartados');
        });
    }
};
