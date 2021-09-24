<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateReferalAvilablityAddOrientationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_referals_availability', function (Blueprint $table) {
            $table->boolean('orientation')->nullable()->after('starting_time');
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
            $table->dropColumn('orientation');
        });
    }
}
