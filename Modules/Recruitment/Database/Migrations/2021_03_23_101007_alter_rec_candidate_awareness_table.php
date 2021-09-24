<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidateAwarenessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_awareness', function (Blueprint $table) {
            $table->unsignedInteger("interview_score")->nullable()->after('english_rating_id');
            $table->unsignedInteger("reference_score")->nullable()->after('interview_score');
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
        Schema::connection('mysql_rec')->table('rec_candidate_awareness', function (Blueprint $table) {
            $table->dropColumn("interview_score");
            $table->dropColumn("reference_score");
            $table->dropColumn("interview_date");
            $table->dropColumn("interview_notes");
            $table->dropColumn("reference_date");
            $table->dropColumn("reference_notes");
        });
    }
}
