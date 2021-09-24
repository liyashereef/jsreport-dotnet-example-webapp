<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateJobsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            $table->integer('job_id')->unsigned();
            //$table->integer('job_reassigned_id')->unsigned()->default(0)->nullable();
            $table->text('fit_assessment_why_apply_for_this_job');
            $table->enum('status', ['Draft', 'Applied']);
            // $table->integer('security_awareness_id')->unsigned()->nullable();
            $table->enum('candidate_status', ['Proceed', 'Reject'])->nullable();
            $table->integer('feedback_id')->unsigned()->nullable();
            $table->float('average_score', 4, 2)->nullable()->default(0.0);
            $table->string('proposed_wage_low',255)->nullable();
            $table->string('proposed_wage_high',255)->nullable();
            $table->integer('job_reassigned_id')->nullable();
            // $table->integer('english_rating_id')->nullable();
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
        Schema::dropIfExists('candidate_jobs');
    }

}
