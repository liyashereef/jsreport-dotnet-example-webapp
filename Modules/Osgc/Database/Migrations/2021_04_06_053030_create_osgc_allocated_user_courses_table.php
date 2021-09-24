<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcAllocatedUserCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_allocated_user_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('id from osgc user table');
            $table->integer('course_id')->unsigned()->comment('id from osgc course  table');
            $table->boolean('status')->default(0)->comment('1=for course completed');
            $table->dateTime('completed_time')->nullable();
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
        Schema::dropIfExists('osgc_allocated_user_courses');
    }
}
