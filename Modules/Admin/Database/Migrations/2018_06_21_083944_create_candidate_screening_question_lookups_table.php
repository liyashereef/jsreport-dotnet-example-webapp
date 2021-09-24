<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidateScreeningQuestionLookupsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('candidate_screening_question_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('category', ['Initiative', 'Stress Tolerance', 'Teamwork / Interpersonal Group Dynamics', 'Scenarios / Problem Solving']);
            $table->text('screening_question');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('candidate_screening_question_lookups');
    }

}
