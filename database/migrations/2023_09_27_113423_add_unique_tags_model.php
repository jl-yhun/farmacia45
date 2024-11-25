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
        Schema::table('tags_models', function (Blueprint $table) {
            $table->unique(['tageable_id', 'tag_id', 'tageable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags_models', function (Blueprint $table) {
            $table->dropUnique('tags_models_tageable_id_tag_id_tageable_type_unique');
        });
    }
};
