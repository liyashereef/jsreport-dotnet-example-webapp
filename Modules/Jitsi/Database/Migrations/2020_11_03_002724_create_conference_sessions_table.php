<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConferenceSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conference_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('roomid');
            $table->string("meetingtitle", 250)->nullable();
            $table->string("description", 1000)->nullable();
            $table->unsignedInteger("customer_id")->nullable();
            $table->unsignedInteger("employee_id")->nullable()->comment("User Table");
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
        Schema::dropIfExists('conference_sessions');
    }
}
