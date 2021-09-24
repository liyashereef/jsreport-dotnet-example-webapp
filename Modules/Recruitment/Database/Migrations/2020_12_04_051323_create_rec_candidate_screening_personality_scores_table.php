<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateScreeningPersonalityScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_screening_personality_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->nullable();
            $table->string('EI')->nullable();
            $table->string('SN')->nullable();
            $table->string('TF')->nullable();
            $table->string('JP')->nullable();
            $table->string('score')->nullable();
            $table->boolean('order')->nullable()->comment('1=Primary, 2=Secondary');
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_screening_personality_scores');
    }
}
