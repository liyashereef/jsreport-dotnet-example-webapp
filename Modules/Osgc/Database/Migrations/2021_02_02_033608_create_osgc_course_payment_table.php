<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcCoursePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_course_payment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('user id from osgc user table');
            $table->integer('course_id')->unsigned()->comment('course id from osgc course table');
            $table->string('amount');
            $table->string('transaction_id');
            $table->integer('status')->nullable();
            $table->dateTime('started_time')->nullable();
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
        Schema::dropIfExists('osgc_course_payment');
    }
}
