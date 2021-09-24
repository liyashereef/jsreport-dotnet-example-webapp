<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeShiftsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('employee_shifts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_shift_payperiod_id')->unsigned()->index('employee_shifts_employee_shift_payperiod_id_foreign');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->time('work_hours');
            $table->text('notes');
            $table->boolean('active')->default(1);
            $table->boolean('submitted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('employee_shifts');
    }

}
