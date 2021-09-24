<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftLiveLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_live_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->dateTime('shift_start_time')->nullable();
            $table->decimal('latitude',11,8)->comment('Latitude')->nullable();
            $table->decimal('longitude',11,8)->comment('Longitude')->nullable();
            $table->float('accuracy')->comment('Accuracy')->nullable();
            $table->float('speed')->comment('Speed')->nullable();
            $table->text('raw_data')->comment('Data passed via api')->nullable();
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('shift_live_locations');
    }
}
