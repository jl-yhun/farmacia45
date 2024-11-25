<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::unprepared('CREATE TRIGGER calculate_total_oc AFTER INSERT ON ordenes_compra_productos FOR EACH ROW
        BEGIN
            UPDATE ordenes_compra 
               SET total = (SELECT SUM(cantidad * compra) 
                             FROM ordenes_compra_productos 
                            WHERE orden_compra_id = NEW.orden_compra_id) 
             WHERE id = NEW.orden_compra_id;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER calculate_total_oc');
    }
};
