<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCourseQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_course_question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_course_question_id')->unsigned()->comment('id from test course questions table');
            $table->string('answer_option',255);
            $table->boolean('is_correct_answer')->default(0)->comment('1=true, 0=false');
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
        Schema::dropIfExists('test_course_question_options');
    }
}
