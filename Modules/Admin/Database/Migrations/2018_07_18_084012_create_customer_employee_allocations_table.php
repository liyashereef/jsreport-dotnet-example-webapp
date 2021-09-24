<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerEmployeeAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_employee_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('customer_employee_allocations_user_id_foreign');
            $table->integer('customer_id')->unsigned()->index('customer_employee_allocations_customer_id_foreign');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
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
        Schema::dropIfExists('customer_employee_allocations');
    }
}
