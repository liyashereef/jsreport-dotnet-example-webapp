<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateJobDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_job_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->nullable();
            $table->integer('job_id')->nullable();
            $table->integer('rec_preference')->nullable();
            $table->decimal('rec_match_score', 6, 2);
            $table->string('estimated_travel_time')->nullable();
            $table->string('estimate_distance')->nullable();
            $table->boolean('status')->nullable();
            $table->integer('recruiter_id')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_job_details');
    }
}
