<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeShiftPayperiodsToAddEmployeeScheduleTimeLogId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_payperiods', function (Blueprint $table) {
            $table->integer('employee_schedule_time_log_id')->nullable()->after('pay_period_id')->comment('This will be ID from EmployeeScheduleTimeLog table, based on shift start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_shift_payperiods', function (Blueprint $table) {
            $table->dropColumn('employee_schedule_time_log_id');
        });
    }
}
