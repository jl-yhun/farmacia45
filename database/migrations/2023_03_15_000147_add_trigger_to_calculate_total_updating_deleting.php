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
        DB::unprepared('CREATE PROCEDURE sp_calculate_total_oc(IN ocId INTEGER)
        BEGIN
            UPDATE ordenes_compra 
               SET total = (SELECT COALESCE(SUM(cantidad * compra), 0)
                             FROM ordenes_compra_productos 
                            WHERE orden_compra_id = ocId) 
             WHERE id = ocId;
        END');

        DB::unprepared('CREATE TRIGGER calculate_total_oc_inserting AFTER INSERT ON ordenes_compra_productos FOR EACH ROW
        BEGIN
            CALL sp_calculate_total_oc(NEW.orden_compra_id);
        END');

        DB::unprepared('CREATE TRIGGER calculate_total_oc_updating AFTER UPDATE ON ordenes_compra_productos FOR EACH ROW
        BEGIN
            CALL sp_calculate_total_oc(NEW.orden_compra_id);
        END');

        DB::unprepared('CREATE TRIGGER calculate_total_oc_deleting AFTER DELETE ON ordenes_compra_productos FOR EACH ROW
        BEGIN
            CALL sp_calculate_total_oc(OLD.orden_compra_id);
        END');

        DB::unprepared('DROP TRIGGER calculate_total_oc');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER calculate_total_oc_updating');
        DB::unprepared('DROP TRIGGER calculate_total_oc_inserting');
        DB::unprepared('DROP TRIGGER calculate_total_oc_deleting');
        DB::unprepared('DROP PROCEDURE sp_calculate_total_oc');

        DB::unprepared('CREATE TRIGGER calculate_total_oc AFTER INSERT ON ordenes_compra_productos FOR EACH ROW
        BEGIN
            UPDATE ordenes_compra 
               SET total = (SELECT SUM(cantidad * compra) 
                             FROM ordenes_compra_productos 
                            WHERE orden_compra_id = NEW.orden_compra_id) 
             WHERE id = NEW.orden_compra_id;
        END');
    }
};
