<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsgcTestUserAttemptedQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('osgc_test_user_attempted_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_user_result_id')->unsigned()->comment('id from osgc_test_user_result table');
            $table->integer('test_course_question_id')->unsigned()->comment('id from osgc_test_course_question table');
            $table->integer('test_course_question_option_id')->nullable()->comment('id from osgc_test_course_question_option table');
            $table->boolean('is_correct_answer')->default(false)->comment('correct_answer = 1');
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
        Schema::dropIfExists('osgc_test_user_attempted_questions');
    }
}