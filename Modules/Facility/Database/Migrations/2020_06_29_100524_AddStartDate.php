<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facility_service_data', function (Blueprint $table) {
            $table->date("start_date")->after("booking_window")->useCurrent = true;
        });
        Schema::table('facility_service_slots', function (Blueprint $table) {
            $table->date("start_date")->after("slot_interval")->useCurrent = true;
        });
        Schema::table('facility_service_timings', function (Blueprint $table) {
            $table->date("start_date")->after("weekend_timing")->useCurrent = true;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facility_service_data', function (Blueprint $table) {
            $table->dropColumn("start_date");
        });
        Schema::table('facility_service_slots', function (Blueprint $table) {
            $table->dropColumn("start_date");
        });
        Schema::table('facility_service_timings', function (Blueprint $table) {
            $table->dropColumn("start_date");
        });
    }
}
