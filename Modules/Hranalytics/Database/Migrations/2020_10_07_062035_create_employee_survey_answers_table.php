<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSurveyAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_survey_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("entry_id");
            $table->unsignedInteger("survey_id");
            $table->unsignedInteger("question_id");
            $table->string("question", 1000);
            $table->enum("answer_type", ["1", "2"]);
            $table->string("answer", 1000);
            $table->unsignedInteger("created_by");
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
        Schema::dropIfExists('employee_survey_answers');
    }
}
