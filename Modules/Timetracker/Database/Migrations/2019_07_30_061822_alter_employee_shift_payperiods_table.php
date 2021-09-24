<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeShiftPayperiodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_payperiods', function (Blueprint $table) {
            $table->time('approved_total_regular_hours')->nullable()->after('total_statutory_hours');
			$table->time('approved_total_overtime_hours')->nullable()->after('approved_total_regular_hours');
			$table->time('approved_total_statutory_hours')->nullable()->after('approved_total_overtime_hours');
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
            $table->dropColumn('approved_total_regular_hours');
			$table->dropColumn('approved_total_overtime_hours');
			$table->dropColumn('approved_total_statutory_hours');
        });
    }
}
