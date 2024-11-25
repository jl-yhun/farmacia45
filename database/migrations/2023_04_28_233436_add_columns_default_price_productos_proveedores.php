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
        Schema::table('productos_proveedores', function (Blueprint $table) {
            $table->boolean('default')->default(false)->after('disponible');
            $table->decimal('precio', 10, 2)->after('default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos_proveedores', function (Blueprint $table) {
            $table->dropColumn('default');
            $table->dropColumn('precio');
        });
    }
};
