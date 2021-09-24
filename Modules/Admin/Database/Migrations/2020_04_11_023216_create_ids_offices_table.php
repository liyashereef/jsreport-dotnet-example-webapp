<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('adress')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_number_ext')->nullable();
            $table->time('office_hours_start_time');
            $table->time('office_hours_end_time');
            $table->text('special_instructions')->nullable();
            $table->smallInteger('intervals')->nullable();
            $table->date('interval_valid_date')->comment('For edit interval validation')->nullable();
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
        Schema::dropIfExists('ids_offices');
    }
}
