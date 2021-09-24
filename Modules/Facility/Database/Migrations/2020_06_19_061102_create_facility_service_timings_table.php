<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityServiceTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_service_timings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type',1000);
            $table->unsignedInteger('model_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('weekend_timing')->default(false);
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
        Schema::dropIfExists('facility_service_timings');
    }
}
