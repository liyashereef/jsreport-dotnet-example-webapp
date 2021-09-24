<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcTestCourseQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_test_course_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('osgc_course_master_id')->unsigned()->comment('id from osgc test course masters table');
            $table->text('test_question');
            $table->boolean('is_mandatory_display')->default(0)->comment('1=mandatory display');
            $table->boolean('status')->default(1)->comment('0=inactive');
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
        Schema::dropIfExists('osgc_test_course_questions');
    }
}
