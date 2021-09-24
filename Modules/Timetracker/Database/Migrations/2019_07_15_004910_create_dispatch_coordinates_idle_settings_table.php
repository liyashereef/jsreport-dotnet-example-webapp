<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispatchCoordinatesIdleSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('dispatch_coordinates_idle_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idle_time')->comment('Idle time in minutes')->default(0);
            $table->integer('user_id')->comment('created by');
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
        Schema::dropIfExists('dispatch_coordinates_idle_settings');
    }
}
