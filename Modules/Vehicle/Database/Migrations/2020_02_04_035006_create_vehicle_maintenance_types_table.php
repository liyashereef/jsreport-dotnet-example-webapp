<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleMaintenanceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_maintenance_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category_id');
            $table->string('name');
            $table->bigInteger('critical_after_km')->nullable();
            $table->integer('critical_after_days')->nullable();
            $table->integer('type')->comment('1=>km,2=>date')->nullable();
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
        Schema::dropIfExists('vehicle_maintenance_types');
    }
}
