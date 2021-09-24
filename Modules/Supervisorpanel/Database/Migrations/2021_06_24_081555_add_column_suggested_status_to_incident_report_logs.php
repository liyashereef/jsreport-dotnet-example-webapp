<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSuggestedStatusToIncidentReportLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_status_logs', function (Blueprint $table) {
            $table->unsignedInteger('suggested_incident_status_list_id')->unsigned()->after('incident_status_list_id');
            $table->tinyInteger('amendment')->after('closed_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_status_logs', function (Blueprint $table) {
            $table->dropColumn('suggested_incident_status_list_id');
            $table->dropColumn('amendment');
        });
    }
}
