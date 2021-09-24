<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSecurityPatrolFenceDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrol_fence_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id')->unsigned()->nullable();
            $table->integer('fence_id')->unsigned()->nullable();
            $table->integer('start_coordinate_id')->unsigned()->nullable();
            $table->integer('end_coordinate_id')->unsigned()->nullable();
            $table->dateTime('time_entry')->nullable();
            $table->dateTime('time_exit')->nullable();
            $table->integer('duration')->nullable();
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
        Schema::dropIfExists('mobile_security_patrol_fence_datas');
    }
}
