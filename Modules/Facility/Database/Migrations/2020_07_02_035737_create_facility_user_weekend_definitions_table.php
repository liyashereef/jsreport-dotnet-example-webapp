<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityUserWeekendDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_user_weekend_definitions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('facility_service_user_allocation_id');
            $table->unsignedInteger('day_id')->comment('Day index of sunday,monday to 0,1');
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
        Schema::dropIfExists('facility_user_weekend_definitions');
    }
}
