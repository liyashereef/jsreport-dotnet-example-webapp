<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimesheetApprovalRatingsTableToAddTimesheetApprovalPayperiodRatingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->integer('timesheet_approval_payperiod_rating_id')->nullable()->after('employee_shift_payperiod_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->dropColumn('timesheet_approval_payperiod_rating_id');
        });
    }
}
