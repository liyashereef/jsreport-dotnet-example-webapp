<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileSecurityPatrols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrols', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id')->nullable();
            $table->integer('customer_id');
            $table->integer('subject_id')->nullable();
            $table->integer('user_id');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_security_patrols');
    }
}
