<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateScreeningPersonalityTestQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_screening_personality_test_question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->text('value')->comment('Option value');
            $table->string('option')->comment('Option A or B');
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
        Schema::dropIfExists('candidate_screening_personality_test_question_options');
    }
}
