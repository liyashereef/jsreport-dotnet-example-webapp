<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('employee_customers_user_id_foreign');
            $table->integer('customer_id')->unsigned()->index('employee_customers_customer_id_foreign');
            $table->date('start_date');
            $table->boolean('active')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('employee_customers');
    }
}
