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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 25)->unique()->nullable(false);
            $table->integer('usuario_id');
            $table->timestamps();
        });

        Schema::create('tags_models', function (Blueprint $table) {
            $table->integer('tageable_id');
            $table->integer('tag_id');
            $table->string('tageable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tags_models');
    }
};
