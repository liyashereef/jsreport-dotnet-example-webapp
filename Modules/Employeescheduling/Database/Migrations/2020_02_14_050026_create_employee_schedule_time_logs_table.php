<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeScheduleTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_schedule_time_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_schedule_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('payperiod_id');
            $table->unsignedInteger('week');
            $table->date('schedule_date');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->decimal('hours',5,2)->nullable();
            $table->boolean('approved')->default(false)->nullable();
            $table->unsignedInteger('approved_by')->default(0)->nullable();
            $table->dateTime('approved_Date')->nullable();
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('employee_schedule_time_logs');
    }
}
