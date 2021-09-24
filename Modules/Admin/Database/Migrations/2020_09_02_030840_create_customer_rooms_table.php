<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->nullable(false);
            $table->string('name', 200)->nullable(false);
            $table->string('machine_name', 250)->nullable(false);
            $table->boolean('active_event')->nullable();
            $table->datetime('event_updated_at')->nullable();
            $table->integer('sensor_trigger_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('customer_rooms');
    }
}
