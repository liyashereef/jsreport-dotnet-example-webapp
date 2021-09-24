<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeShiftPayperiodsWithBillableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_payperiods', function (Blueprint $table) {
            $table->time('billable_overtime_hours')->nullable()->after('approved_total_statutory_hours');
            $table->time('billable_statutory_hours')->nullable()->after('billable_overtime_hours');
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
            $table->dropColumn('billable_overtime_hours');
            $table->dropColumn('billable_statutory_hours');
        });
    }
}
