<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateReferalsAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_referals_availability', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->integer('job_post_finding')->unsigned()->nullable();
            $table->string("sponser_email", 255)->nullable();
            $table->unsignedInteger("position_availibility")->nullable();
            $table->integer("floater_hours")->nullable();
            $table->unsignedInteger("starting_time")->nullable();
            $table->enum('orientation', ['Yes', 'No'])->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_referals_availability');
    }
}
