<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSecurityPatrolTripCoordinatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrol_trip_coordinates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mobile_security_patrol_trips_id')->comment('Foreign key to mobile_security_patrol_trips');
            $table->decimal('latitude',10,8)->comment('Latitude')->nullable();
            $table->decimal('longitude',10,8)->comment('Longitude')->nullable();
            $table->float('accuracy')->comment('Accuracy')->nullable();
            $table->float('speed')->comment('Speed')->nullable();
            $table->text('raw_data')->comment('Data passed via api')->nullable();
            $table->dateTime('time')->comment('Captured Time')->nullable();
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
        Schema::dropIfExists('mobile_security_patrol_trip_coordinates');
    }
}
