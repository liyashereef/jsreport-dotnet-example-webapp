<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeScheduleTemporaryStoragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_schedule_temporary_storages', function (Blueprint $table) {
            $table->increments('id');
            $table->string("scheduleid",15);
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('payperiod');
            $table->unsignedInteger('employeeid');
            $table->unsignedInteger('week')->default(1)->nullable();
            $table->decimal('hours',5,2)->default(0)->nullable();
            $table->date('scheduledate');
            $table->dateTime('starttime');
            $table->dateTime('endtime');
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('employee_schedule_temporary_storages');
    }
}
