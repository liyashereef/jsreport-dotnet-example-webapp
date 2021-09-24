<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeShiftPayperiodsTableToAddIsRated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_payperiods', function (Blueprint $table) {
            $table->boolean('is_rated')->nullable()->after('weekly_performance');
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
            $table->dropColumn('is_rated');
        });
    }
}
