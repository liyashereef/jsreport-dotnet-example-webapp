<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_triggers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('room_id');
            $table->integer('sensor_id');
            $table->dateTime('trigger_started_at');
            $table->dateTime('trigger_ended_at')->nullable();
            $table->integer('sleep_after_trigger')
                ->comment('Time in minutes the sensor must be inactive after detection setting');
            $table->integer('end_trigger_after')
                ->comment('Time in minutes the sensor must be trigger event end setting');
            $table->integer('incident_id')->nullable();
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
        Schema::dropIfExists('sensor_triggers');
    }
}
