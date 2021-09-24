<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type',1000);
            $table->unsignedInteger('model_id');
            $table->unsignedInteger('facility_user_id');
            $table->dateTime('booking_date_start');
            $table->dateTime('booking_date_end');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('facility_bookings');
    }
}
