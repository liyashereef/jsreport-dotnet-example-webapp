<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateJobAddInterviewDateAndNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_jobs', function (Blueprint $table) {
            $table->date("interview_date")->nullable()->after('interview_score');
            $table->mediumText("interview_notes")->nullable()->after('interview_date');
            $table->date("reference_date")->nullable()->after('reference_score');
            $table->mediumText("reference_notes")->nullable()->after('reference_date');
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
            $table->dropColumn("interview_date");
            $table->dropColumn("interview_notes");
            $table->dropColumn("reference_date");
            $table->dropColumn("reference_notes");
        });
    }
}
