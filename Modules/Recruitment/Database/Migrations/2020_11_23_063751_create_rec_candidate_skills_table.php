<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->integer('skill_id')->unsigned();
            $table->enum('skill_level', ['No Knowledge', 'Basic Knowledge', 'Good Knowledge', 'Advanced Knowledge', 'Expert Knowledge']);
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_skills');
    }
}
