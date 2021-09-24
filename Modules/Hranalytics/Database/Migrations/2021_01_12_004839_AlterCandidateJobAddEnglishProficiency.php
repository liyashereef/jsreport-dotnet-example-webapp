<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateJobAddEnglishProficiency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_jobs', function (Blueprint $table) {
            $table->unsignedInteger("interview_score")->nullable()->after('english_rating_id');
            $table->unsignedInteger("reference_score")->nullable()->after('interview_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_jobs', function (Blueprint $table) {
            $table->dropColumn("interview_score");
            $table->dropColumn("reference_score");
        });
    }
}
