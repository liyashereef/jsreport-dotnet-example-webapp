<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScheduleCustomerMultipleFillShifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_customer_multiple_fill_shifts', function (Blueprint $table) {
            $table->unsignedInteger("parent_id")->default(0)->after("shift_timing_id");
            $table->unsignedInteger("no_of_position")->default(0)->after("parent_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_customer_multiple_fill_shifts', function (Blueprint $table) {
            $table->dropColumn("parent_id");
            $table->dropColumn("no_of_position");
        });
    }
}
