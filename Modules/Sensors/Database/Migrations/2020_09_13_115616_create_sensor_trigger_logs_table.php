<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorTriggerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_trigger_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sensor_id');
            $table->integer('sensor_trigger_id');
            $table->dateTime('trigger_started_at');
            $table->dateTime('trigger_ended_at')->nullable();
            $table->integer('created_by');
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
        Schema::dropIfExists('sensor_trigger_logs');
    }
}
