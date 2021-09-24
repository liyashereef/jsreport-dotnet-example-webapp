<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmittedDateToCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('candidate_jobs', function (Blueprint $table) {
            $table->timestamp('submitted_date')->nullable()->after('status');
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
            $table->dropColumn('submitted_date');
         });
    }
}
