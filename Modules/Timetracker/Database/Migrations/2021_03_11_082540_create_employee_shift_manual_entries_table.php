<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeShiftManualEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_shift_manual_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payperiod_id');
            $table->unsignedInteger('payperiod_week');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('cpid_rate_id');
            $table->unsignedInteger('cpid_function_id');
            $table->unsignedInteger('work_hour_type_id');
            $table->unsignedInteger('work_hour_activity_code_customer_id');
            $table->unsignedInteger('hours');
            $table->unsignedInteger('total_amount');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('employee_shift_manual_entries');
    }
}
