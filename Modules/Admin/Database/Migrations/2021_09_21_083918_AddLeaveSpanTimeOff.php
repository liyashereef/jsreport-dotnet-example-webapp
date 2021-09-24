<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeaveSpanTimeOff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_timeoff', function (Blueprint $table) {
            $table->string("start_time")->nullable()->change();
            $table->string("end_time")->nullable()->change();
            $table->unsignedInteger("leave_duration")->default(0)->after('end_time')->comment("0=>full Day,1=>Half day Morning,2=>Half Day Evening,
            3=>1st Quarter,4=>2nd Quarter,
            5=>3rd Quarter,6=>4th Quarter");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_timeoff', function (Blueprint $table) {
            $table->dropColumn("leave_duration");
        });
    }
}
