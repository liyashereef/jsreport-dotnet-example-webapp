<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeShiftApprovalLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_shift_approval_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_shift_payperiod_id');
            $table->text('cpid');
            $table->time('total_regualr_hours');
            $table->time('total_overtime_hours');
            $table->time('total_statutory_hours');
            $table->integer('approved_by')->unsigned();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('employee_shift_approval_logs');
    }
}
