<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject')->nullable();
            $table->integer('dispatch_request_type_id')->unsigned();
            $table->integer('customer_id')->unsigned();
            $table->string('site_address')->nullable();
            $table->string('site_postalcode')->nullable();
            $table->float('rate')->default(0);
            $table->integer('created_by')->comment('ID from user table')->unsigned();
            $table->text('description')->nullable();
            $table->integer('dispatch_request_status_id')->comment('1 = Open, 2 = In Progress, 3= Arrived & Started Investigation, 4 = Closed');
            $table->integer('respond_by')->comment('ID from user table')->nullable();
            $table->dateTime('respond_at')->nullable();
            $table->time('estimated_time')->nullable();
            $table->time('actual_time')->nullable();
            $table->time('delta')->nullable();
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
        Schema::dropIfExists('dispatch_requests');
    }
}
