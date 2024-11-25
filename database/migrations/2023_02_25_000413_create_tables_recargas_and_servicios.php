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
        Schema::create('recargas', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('monto', 10, 2);
            $table->enum('compania', ['Telcel', 'Movistar', 'AT&T', 'Otra']);
            $table->timestamps();
        });

        Schema::create('servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('monto', 10, 2);
            $table->decimal('comision', 10, 2);
            $table->string('servicio', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recargas');
        Schema::dropIfExists('servicios');
    }
};
