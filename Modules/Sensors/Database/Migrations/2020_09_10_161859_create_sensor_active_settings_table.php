<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorActiveSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor_active_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('room_id');
            $table->integer('day_id');
            $table->boolean('is_active');
            $table->string('start_time');
            $table->string('end_time');
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
        Schema::dropIfExists('sensor_active_settings');
    }
}
