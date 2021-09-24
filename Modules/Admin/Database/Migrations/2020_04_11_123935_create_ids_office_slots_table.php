<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsOfficeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_office_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('display_name');
            $table->integer('ids_office_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('valid_till_date')->comment('For edit interval validation')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('ids_office_slots');
    }
}
