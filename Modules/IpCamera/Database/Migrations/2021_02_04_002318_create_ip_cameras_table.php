<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpCamerasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_cameras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200)->nullable(false);
            $table->string('nod_mac', 200)->nullable(false);
            $table->string('pan_mac', 200)->nullable();
            $table->string('gateway_mac', 200)->nullable();
            $table->string('machine_name', 250)->nullable(false);
            $table->boolean('online')->nullable();
            $table->dateTime('online_updated_at')->nullable();
            $table->dateTime('latest_detection_at')->nullable();
            $table->boolean('low_battery')->nullable();
            $table->dateTime('low_battery_updated_at')->nullable();
            $table->integer('room_id')->nullable();
            $table->dateTime('room_allocated_at')->nullable();
            $table->boolean('enabled')->default(true)->nullable(false);
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('ip_cameras');
    }
}
