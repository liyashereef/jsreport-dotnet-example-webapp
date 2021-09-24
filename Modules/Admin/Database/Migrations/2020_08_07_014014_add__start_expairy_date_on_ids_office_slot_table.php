<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartExpairyDateOnIdsOfficeSlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_office_slots', function (Blueprint $table) {
            $table->unsignedInteger('ids_office_timing_id')->after('id')->nullable();
        });
        Schema::table('ids_offices', function (Blueprint $table) {
            $table->time('office_hours_start_time')->nullable()->change();
            $table->time('office_hours_end_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_office_slots', function (Blueprint $table) {
            $table->dropColumn("ids_office_timing_id");
        });
        Schema::table('ids_offices', function (Blueprint $table) {
            $table->time('office_hours_start_time')->nullable(false)->change();
            $table->time('office_hours_end_time')->nullable(false)->change();
        });
    }
}
