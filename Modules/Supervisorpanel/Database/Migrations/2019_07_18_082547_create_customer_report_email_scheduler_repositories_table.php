<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerReportEmailSchedulerRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_report_email_scheduler', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customerid');
            $table->integer('payperiodid');
            $table->date('payperioddate');
            $table->string('supervisormail');
            $table->date('maildate');
            $table->boolean('sendflag')->default(0);
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('customer_report_email_scheduler');
    }
}
