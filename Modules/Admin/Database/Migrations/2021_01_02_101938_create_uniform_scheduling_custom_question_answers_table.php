<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformSchedulingCustomQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_scheduling_custom_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uniform_scheduling_entry_id');
            $table->integer('uniform_scheduling_custom_question_id')->unsigned();
            $table->text('custom_questions_str');
            $table->integer('uniform_scheduling_custom_option_id')->unsigned();
            $table->string('custom_option_str',150);
            $table->string('other_value')->nullable();
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
        Schema::dropIfExists('uniform_scheduling_custom_question_answers');
    }
}
