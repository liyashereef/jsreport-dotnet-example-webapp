<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTimeoffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_timeoff', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_number');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('cpidRate_id');
            $table->date('start_date');
            $table->string('start_time');
            $table->date('end_date');
            $table->string('end_time');
            $table->unsignedInteger('reason_id');
            $table->unsignedInteger('backfillstatus');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('mail_send');
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
        Schema::dropIfExists('employee_timeoff');
    }
}
