<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateJobsToAddEnglsihRatingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         //
         Schema::table('candidate_jobs',function($table){

            $table->integer('english_rating_id')->nullable()->after('job_reassigned_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('candidate_jobs',function($table){

            $table->dropColumn('english_rating_id');
        });
    }
}
