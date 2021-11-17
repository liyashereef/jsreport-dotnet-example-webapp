<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorLogScreeningSubmissionQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_screening_submission_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_log_screening_submission_id')->nullable(false);
            $table->integer('visitor_log_screening_template_question_id')->nullable(false);
            $table->boolean('answer');
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
        Schema::dropIfExists('visitor_log_screening_submission_question_answers');
    }
}
