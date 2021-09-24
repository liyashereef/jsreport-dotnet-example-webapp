<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformSchedulingOfficeTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_scheduling_office_timings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('uniform_scheduling_office_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->smallInteger('intervals')->comment('In minutes')->nullable();
            $table->date('start_date')->nullable();
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
        Schema::dropIfExists('uniform_scheduling_office_timings');
    }
}
