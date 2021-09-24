<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTermsAndConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_terms_and_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->comment('customer id from customers, O= Default')->nullable();
            $table->integer('type_id')->default('1')->comment('1-For visitor log');
            $table->text('terms_and_conditions')->nullable();
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
        Schema::dropIfExists('customer_terms_and_conditions');
    }
}
