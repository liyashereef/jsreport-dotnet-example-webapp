<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportTerminationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_terminations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('candidate_id')->unsigned()->nullable();
            $table->integer('employee_exit_interview_id')->unsigned()->nullable();
            $table->integer('age')->unsigned()->nullable();
            $table->string('education_1', 2000)->nullable();
            $table->string('education_2', 2000)->nullable();
            $table->string('education_3', 2000)->nullable();
            $table->double('screening_questions_avg_count')->nullable();
            $table->string('length_of_service', 191)->nullable();
            $table->integer('no_of_guards')->nullable();
            $table->string('position', 191)->nullable();
            $table->double('current_wage_1')->nullable();
            $table->double('current_wage_2')->nullable();
            $table->double('current_wage_3')->nullable();
            $table->string('distance_between_work_and_home', 191)->nullable();
            $table->string('time_between_work_and_home', 191)->nullable();
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
        Schema::dropIfExists('report_terminations');
    }
}
