<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsCustomQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_custom_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ids_entry_id');
            $table->integer('ids_custom_questions_id')->unsigned();
            $table->string('ids_custom_questions_str',500);
            $table->integer('ids_custom_option_id')->unsigned();
            $table->string('ids_custom_option_str',150);
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
        Schema::dropIfExists('ids_custom_question_answers');
    }
}
