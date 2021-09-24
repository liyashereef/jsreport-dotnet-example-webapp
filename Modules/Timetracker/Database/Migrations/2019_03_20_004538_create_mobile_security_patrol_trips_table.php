<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSecurityPatrolTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrol_trips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id')->comment('Shift corresponding to the trip');
            $table->dateTime('start_time')->comment('Start time')->nullable();
            $table->dateTime('end_time')->comment('End time')->nullable();
            $table->string('starting_location')->comment('Starting Location')->nullable();
            $table->string('destination')->comment('Destination')->nullable();
            $table->integer('travel_time')->comment('Total travel time for the trip')->nullable();
            $table->float('total_km')->comment('Total km covered for the trip')->nullable();
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
        Schema::dropIfExists('mobile_security_patrol_trips');
    }
}
