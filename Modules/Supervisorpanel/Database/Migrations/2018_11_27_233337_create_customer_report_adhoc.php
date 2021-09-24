<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerReportAdhoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_report_adhocs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned()->comments('ID from employees')->nullable();
            $table->date('date')->nullable();
            $table->integer('hours_off')->nullable();
            $table->integer('reason_id')->unsigned()->comments('ID from leave reasons')->nullable();
            $table->text('notes', 1000)->nullable();
            $table->integer('customer_payperiod_template_id')->nullable();
            $table->integer('payperiod_id')->nullable();            
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
        Schema::dropIfExists('customer_report_adhocs');
    }
}
