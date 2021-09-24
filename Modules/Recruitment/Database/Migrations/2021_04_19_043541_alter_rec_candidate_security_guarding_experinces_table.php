<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidateSecurityGuardingExperincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')
            ->table('rec_candidate_security_guarding_experinces', function (Blueprint $table) {
                $table->string('test_score_path')
                ->nullable()
                ->after('test_score_document_id');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::connection('mysql_rec')->table('rec_candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->dropColumn('test_score_path');
         });
    }
}
