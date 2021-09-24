<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateJobsToAddSecurityAwarenessId extends Migration
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

            $table->integer('security_awareness_id')->nullable()->after('brand_awareness_id');
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

            $table->dropColumn('security_awareness_id');
        });
    }
}
