<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleMaintenanceIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('vehicle_maintenance_intervals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_type_id');
            $table->integer('service_km')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->date('service_date')->nullable();
            $table->integer('interval_km')->nullable();
            $table->integer('interval_day')->nullable();
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
         Schema::dropIfExists('vehicle_maintenance_intervals');
    }
}
