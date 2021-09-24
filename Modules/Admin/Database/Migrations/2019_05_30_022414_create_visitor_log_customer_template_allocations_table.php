<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorLogCustomerTemplateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_customer_template_allocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->unsigned()->index('visitor_log_customer_template_allocations_template_id_foreign');
            $table->integer('customer_id')->unsigned()->index('visitor_log_customer_template_allocations_customer_id_foreign');
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
        Schema::dropIfExists('visitor_log_customer_template_allocations');
    }
}
