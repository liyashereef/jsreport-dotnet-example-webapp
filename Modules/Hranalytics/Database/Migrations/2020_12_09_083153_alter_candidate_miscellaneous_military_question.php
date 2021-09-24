<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateMiscellaneousMilitaryQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_miscellaneouses', function (Blueprint $table) {
             $table->enum('spouse_of_armedforce', ['No', 'Yes'])->default('No')->after('reason_for_release');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_miscellaneouses', function (Blueprint $table) {
            $table->dropColumn('spouse_of_armedforce');
        });
    }
}
