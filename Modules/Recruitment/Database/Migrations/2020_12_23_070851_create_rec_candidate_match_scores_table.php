<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateMatchScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_match_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('criteria_id')->nullable();
            $table->integer('criteria_weight');
            $table->string('premium')->nullable();
            $table->decimal('mapping_value', 6, 2);
            $table->decimal('weighted_score', 6, 2);
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_match_scores');
    }
}
