<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateSecurityGuardingExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->decimal('test_score_percentage', 15, 2)->nullable()->after('expiry_cpr');
            $table->string('test_score_document_id', 255)->nullable()->after('test_score_percentage');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->dropColumn('test_score_percentage');
            $table->dropColumn('test_score_document_id');
        });
    }
}
