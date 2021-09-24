<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEmployeeScheduleTimeLogsTableAddOverlaps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_schedule_time_logs', function (Blueprint $table) {
            $table->boolean('overlaps')->default(false)->after('approved_Date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_schedule_time_logs', function (Blueprint $table) {
            $table->dropColumn('overlaps');
        });
    }
}
