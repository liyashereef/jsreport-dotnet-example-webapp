<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleDamageAttachmentsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_damage_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id')->unsigned()->index('vehicle_damage_attachments_trips_id_foreign');
            $table->integer('vehicle_damage_time')->unsigned();
            $table->integer('attachment_id')->unsigned()->index('vehicle_damage_attachments_attachment_id_foreign');
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
        Schema::dropIfExists('vehicle_damage_attachments');
    }
}
