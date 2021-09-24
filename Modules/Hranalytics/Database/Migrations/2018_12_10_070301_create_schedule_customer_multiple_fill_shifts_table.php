<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleCustomerMultipleFillShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_customer_multiple_fill_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_customer_requirement_id')->unsigned();
            $table->integer('assigned_employee_id')->unsigned()->nullable();
            $table->integer('shift_timing_id')->unsigned();
            $table->string('shift_from', 255);
            $table->string('shift_to', 255);
            $table->boolean('assigned');
            $table->integer('assigned_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_customer_multiple_fill_shifts');
    }
}
