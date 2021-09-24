<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcUserCourseCertificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_user_course_certification', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->comment('user id from osgc user table');
                $table->integer('course_id')->unsigned()->comment('course id from osgc course table');
                $table->string('certificate_name');
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
        Schema::dropIfExists('osgc_user_course_certification');
    }
}
