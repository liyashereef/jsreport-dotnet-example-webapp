<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionStrOnVisitorLogScreeningSubmissionQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_screening_submission_question_answers', function (Blueprint $table) {
            $table->string('visitor_log_screening_template_question_str')->nullable()->after('answer')
            ->comment('question');

            $table->boolean('visitor_log_screening_template_question_expected_answer')->nullable()
            ->after('visitor_log_screening_template_question_str')->comment('expected answer');

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
            $table->dropColumn('visitor_log_screening_template_question_str');
            $table->dropColumn('visitor_log_screening_template_question_expected_answer');
        });
    }
}
