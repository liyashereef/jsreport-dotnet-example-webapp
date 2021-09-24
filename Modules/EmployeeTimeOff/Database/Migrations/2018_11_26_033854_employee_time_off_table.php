<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmployeeTimeOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_time_off', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('employee_role_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->integer('supervisor_id')->unsigned()->nullable();
            $table->integer('areamanager_id')->unsigned()->nullable();
            $table->integer('hr_id')->nullable();
            $table->string('oc_email', 255)->nullable();
            $table->integer('request_type_id')->unsigned();
            $table->boolean('vacation_pay_required')->nullable();
            $table->string('vacation_pay_amount')->nullable();
            $table->integer('vacation_payperiod_id')->nullable();
            $table->date('vacation_pay_date')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->double('no_of_shifts', 6, 2)->nullable();
            $table->double('average_shift_length', 6, 2)->nullable();
            $table->double('total_hours_away', 6, 2)->nullable();
            $table->integer('leave_reason_id')->nullable();
            $table->string('other_reason', 255)->nullable();
            $table->text('nature_of_request')->nullable();
            $table->integer('request_category_id')->nullable();
            $table->integer('days_requested')->nullable();
            $table->integer('days_approved')->nullable();
            $table->integer('days_rejected')->nullable();
            $table->integer('days_remaining')->nullable();
            $table->boolean('hr_approved')->nullable()->comment('If HR approved');
            $table->integer('approved_by')->nullable()->comment('Reporting AM');

            $table->string('status')->nullable()->comment('Current status of the application');
            $table->boolean('approved')->nullable()->comment('Is the request approved - final status');
            $table->integer('current_level')->nullable()->comment('Current level status of request from workflow');
            $table->integer('pending_with_emp')->nullable()->comment('Employee id with which the request is pending');
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
        Schema::dropIfExists('employee_time_off');
    }
}
