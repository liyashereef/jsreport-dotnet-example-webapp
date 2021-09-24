<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduledEmployeeWorkHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_employee_work_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_schedule_id');
            $table->unsignedInteger('payperiod_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('week');
            $table->decimal('workhours',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_employee_work_hours');
    }
}
