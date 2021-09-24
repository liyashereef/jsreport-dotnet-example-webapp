<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncidentStatusLogIncidentReportForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_status_logs', function (Blueprint $table) {
            $table->foreign('incident_report_id')->references('id')->on('incident_reports')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_status_logs', function(Blueprint $table)
        {
            $table->dropForeign('incident_status_logs_incident_report_id_foreign');
        });  
    }
}
