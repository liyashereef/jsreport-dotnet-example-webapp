<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorLogDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->string('uid')->nullable()->comment('For reference');
            $table->string('activation_code')->nullable()->comment('For divice activation code');
            $table->dateTime('activated_at')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('device_id')->nullable();
            $table->dateTime('last_active_time')->nullable();
            $table->boolean('is_blocked')->default(0);
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('visitor_log_devices');
    }
}
