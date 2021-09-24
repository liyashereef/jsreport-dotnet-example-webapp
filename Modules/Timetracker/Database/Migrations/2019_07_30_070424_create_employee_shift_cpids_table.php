<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeShiftCpidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_shift_cpids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cpid');
            $table->integer('employee_shift_payperiod_id');
            $table->integer('work_hour_type_id');
            $table->integer('cpid_rate_id');
            $table->integer('employee_id');
            $table->time('hours');
            $table->decimal('total_amount',10,3);

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
        Schema::dropIfExists('employee_shift_cpids');
    }
}
