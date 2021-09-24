<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestCourseQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_course_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_course_master_id')->unsigned()->comment('id from test course masters table');
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
        Schema::dropIfExists('test_course_questions');
    }
}
