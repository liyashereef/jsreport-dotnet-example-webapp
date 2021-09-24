<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerTableAddMotionSensorEnabledColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('motion_sensor_enabled')->default(0)->after('facility_booking');
            $table->integer('motion_sensor_incident_subject')->nullable()->after('motion_sensor_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('motion_sensor_incident_subject');
            $table->dropColumn('motion_sensor_enabled');
        });
    }
}
