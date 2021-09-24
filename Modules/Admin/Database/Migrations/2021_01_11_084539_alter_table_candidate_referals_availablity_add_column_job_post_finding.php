<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCandidateReferalsAvailablityAddColumnJobPostFinding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_referals_availability', function (Blueprint $table) {
            $table->integer('job_post_finding')->unsigned()->nullable()->after('candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_referals_availability', function (Blueprint $table) {
            $table->dropColumn('job_post_finding');
        });
    }
}
