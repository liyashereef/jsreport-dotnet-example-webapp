<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSchedulePayperiodAverageHoursTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('employee_schedule_payperiod_average_hours', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_schedule_id');
            $table->unsignedInteger('payperiod_id');
            $table->decimal('average_hours',5,2)->nullable();
            $table->decimal('contractual_hours',5,2)->nullable();
            $table->unsignedInteger('week');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('employee_schedule_payperiod_average_hours');
    }

}
