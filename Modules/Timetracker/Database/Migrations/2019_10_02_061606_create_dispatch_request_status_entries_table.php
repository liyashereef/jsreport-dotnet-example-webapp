<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchRequestStatusEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatch_request_status_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dispatch_request_id')->unsigned();
            $table->integer('dispatch_request_status_id')->unsigned();
            $table->integer('respond_by')->comment('ID from user table')->unsigned();
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
        Schema::dropIfExists('dispatch_request_status_entries');
    }
}
