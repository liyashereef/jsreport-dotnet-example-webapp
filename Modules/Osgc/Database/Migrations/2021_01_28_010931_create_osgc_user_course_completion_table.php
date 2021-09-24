<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcUserCourseCompletionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_user_course_completion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('id from osgc_test_user_result table');
            $table->integer('course_section_id')->unsigned()->comment('id from osgc course section table');
            $table->integer('course_header_id')->unsigned()->comment('id from osgc course header table');
            $table->integer('test_started')->unsigned()->nullable()->comment('1=test started');
            $table->integer('test_completed')->unsigned()->nullable()->comment('1=test completed');
            $table->boolean('content_started')->default(0)->comment('1=content started');
            $table->boolean('content_completed')->default(0)->comment('1=content completed');
            $table->boolean('status')->default(0)->comment('1=course for particular section completed');
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
        Schema::dropIfExists('osgc_user_course_completion');
    }
}
