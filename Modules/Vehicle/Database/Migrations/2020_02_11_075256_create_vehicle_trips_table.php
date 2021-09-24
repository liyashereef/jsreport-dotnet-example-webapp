<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_trips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id')->nullable();
            $table->integer('vehicle_id');
            $table->integer('customer_id');
            $table->integer('user_odometer_start')->nullable();
            $table->integer('user_odometer_end')->nullable();
            $table->integer('user_distance_travelled')->nullable();
            $table->integer('system_odometer_start')->nullable();
            $table->integer('system_odometer_end')->nullable();
            $table->integer('system_distance_travelled')->nullable();
            $table->boolean('start_visible_damage')->nullable();
            $table->boolean('end_visible_damage')->nullable();
            $table->text('start_notes')->nullable();
            $table->text('end_notes')->nullable();
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
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
        Schema::dropIfExists('vehicle_trips');
    }
}
