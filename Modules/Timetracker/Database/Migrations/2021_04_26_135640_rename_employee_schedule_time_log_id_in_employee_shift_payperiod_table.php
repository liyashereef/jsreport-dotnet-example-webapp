<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameEmployeeScheduleTimeLogIdInEmployeeShiftPayperiodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_payperiods', function(Blueprint $table)
        {
            $table->renameColumn('employee_schedule_time_log_id', 'employee_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_shift_payperiods', function(Blueprint $table)
        {
            $table->renameColumn('employee_schedule_id', 'employee_schedule_time_log_id');
        });
    }
}
