<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateAwarenessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_awareness', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->unsigned();
            //$table->integer('job_id')->unsigned();
            $table->string('prefered_hours_per_week', 255)->nullable();
            $table->text('fit_assessment_why_apply_for_this_job');
            $table->enum('status', ['Draft', 'Applied']);
            $table->timestamp('submitted_date')->nullable();
            $table->integer('brand_awareness_id')->nullable();
            $table->integer('security_awareness_id')->unsigned()->nullable();
            $table->enum('candidate_status', ['Proceed', 'Reject'])->nullable();
            $table->integer('feedback_id')->unsigned()->nullable();
            $table->float('average_score', 4, 2)->nullable()->default(0.0);
            $table->string('proposed_wage', 255)->nullable();
            //$table->string('proposed_wage_high',255)->nullable();
            $table->integer('job_reassigned_id')->nullable();
            $table->integer('english_rating_id')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_awareness');
    }
}
