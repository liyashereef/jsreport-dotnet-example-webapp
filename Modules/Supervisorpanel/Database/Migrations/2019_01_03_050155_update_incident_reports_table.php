<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIncidentReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->string('time_of_day', 100)->after('source')->comments('Morning,Afternoon,Evening');
            $table->timestamp('occurance_datetime')->after('time_of_day')->nullable()->comments('Combineddate,month,year,time');
            $table->boolean('incident_report_uploaded')->after('occurance_datetime')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropColumn('time_of_day');
            $table->dropColumn('occurance_datetime');
            $table->dropColumn('incident_report_uploaded');
        });
    }
}
