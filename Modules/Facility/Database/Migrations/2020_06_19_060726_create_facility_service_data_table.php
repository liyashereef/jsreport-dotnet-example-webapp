<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityServiceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_service_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type',1000);
            $table->unsignedInteger('model_id');
            $table->boolean('weekend_booking')->default(true);
            $table->float('maxbooking_perday',5,2)->default(1);
            $table->float('tolerance_perslot',5,2)->default(1);
            $table->unsignedInteger('booking_window')->default(5)->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('facility_service_data');
    }
}
