<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldMobileSecurityPatrolIncidentReportedToEmployeeShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->boolean('mobile_security_patrol_incident_reported')->nullable()->after('submitted');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_shifts', function (Blueprint $table) {
            $table->dropColumn('mobile_security_patrol_incident_reported');
        });
    }
}
