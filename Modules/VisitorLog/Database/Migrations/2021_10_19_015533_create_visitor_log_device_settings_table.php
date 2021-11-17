<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorLogDeviceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_device_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_log_device_id')->nullable();
            $table->string('device_id')->nullable();
            $table->integer('template_id')->nullable();
            $table->string('pin')->nullable();
            $table->boolean('camera_mode')->default(1)->comment('1 = front camera, 0 = rear camera');
            $table->boolean('scaner_camera_mode')->default(1)->comment('1 = front camera, 0 = rear camera');
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
        Schema::dropIfExists('visitor_log_device_settings');
    }
}
