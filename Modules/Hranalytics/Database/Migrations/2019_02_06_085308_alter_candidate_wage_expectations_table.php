<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCandidateWageExpectationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_wage_expectations', function (Blueprint $table) {
            $table->text('security_provider_strengths')->nullable()->after('explanation_wage_expectation');
            $table->text('security_provider_notes')->nullable()->after('security_provider_strengths');
            $table->integer('rate_experience')->nullable()->after('security_provider_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_wage_expectations', function (Blueprint $table) {
            $table->dropColumn('security_provider_strengths');
            $table->dropColumn('security_provider_notes');
            $table->dropColumn('rate_experience');
        });
    }
}
