<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateScreeningCompetencyMatrix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_screening_competency_matrix', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->nullable();
            $table->integer('competency_matrix_lookup_id')->nullable();
            $table->integer('competency_matrix_rating_lookup_id')->nullable();
            $table->integer('superviosr_rating_lookup_id')->nullable();
            $table->text('notes')->nullable();
            $table->integer('rated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidate_screening_competency_matrix');
    }
}
