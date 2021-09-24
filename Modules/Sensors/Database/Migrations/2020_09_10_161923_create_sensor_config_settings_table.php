<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorConfigSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_config_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sleep_after_trigger')
                ->comment('Time in minutes the sensor must be inactive after detection');
            $table->integer('end_trigger_after')
                ->comment('Time in minutes the sensor must be trigger event end');
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('sensor_config_settings');
    }
}
