<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleMaintenanceAttachmentsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_maintenance_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('maintenance_id')->unsigned()->index('vehicle_maintenance_attachments_maintenance_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('vehicle_maintenance_attachments_attachment_id_foreign');
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
        Schema::dropIfExists('vehicle_maintenance_attachments');
    }
}
