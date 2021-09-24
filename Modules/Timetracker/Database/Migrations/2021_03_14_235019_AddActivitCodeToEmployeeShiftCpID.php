<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActivitCodeToEmployeeShiftCpID extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_shift_cpids', function (Blueprint $table) {
            $table->integer("activity_code_id")->nullable()->after("work_hour_type_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_shift_cpids', function (Blueprint $table) {
            $table->dropColumn("activity_code_id");
        });
    }
}
