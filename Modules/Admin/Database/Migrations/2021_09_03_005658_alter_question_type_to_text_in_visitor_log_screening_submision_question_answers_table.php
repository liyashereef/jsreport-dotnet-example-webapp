<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuestionTypeToTextInVisitorLogScreeningSubmisionQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_screening_submission_question_answers', function (Blueprint $table) {
            $table->text('visitor_log_screening_template_question_str')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_screening_submission_question_answers', function (Blueprint $table) {
            $table->string('visitor_log_screening_template_question_str',500)->change();
        });
    }
}
