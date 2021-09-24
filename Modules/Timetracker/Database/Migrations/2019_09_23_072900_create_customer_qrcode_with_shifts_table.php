<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerQrcodeWithShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_qrcode_with_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('shift_id')->nullable();
            $table->integer('qrcode_id')->nullable();
            $table->decimal('latitude',11,8)->comment('Latitude')->nullable();
            $table->decimal('longitude',11,8)->comment('Longitude')->nullable();
            $table->string('image',300)->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('time')->nullable();
            $table->string('no_of_attempts')->nullable();
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
        Schema::dropIfExists('customer_qrcode_with_shifts');
    }
}
