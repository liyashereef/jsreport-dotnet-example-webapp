<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimesheetApprovalConfigurationsTableToAddPreviousWeekEnable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheet_approval_configurations', function (Blueprint $table) {
            $table->boolean('is_previous_week_enabled')->default(true)->after('email_3_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timesheet_approval_configurations', function (Blueprint $table) {
            $table->dropColumn('is_previous_week_enabled');
        });
    }
}
