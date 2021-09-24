<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIncidentReportSubjectAddShortName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_report_subjects', function (Blueprint $table) {
            $table->string('subject_short_name',100)->after('subject')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_report_subjects', function (Blueprint $table) {
            $table->dropColumn('subject_short_name');
        });
    }
}
