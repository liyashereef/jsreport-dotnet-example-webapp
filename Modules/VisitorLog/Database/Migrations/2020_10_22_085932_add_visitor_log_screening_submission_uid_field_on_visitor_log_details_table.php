<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisitorLogScreeningSubmissionUidFieldOnVisitorLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->string('visitor_log_screening_submission_uid')->nullable()->after('additional_comments')->comment('visitor_log_screening_submissions table uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->dropColumn('visitor_log_screening_submission_uid');
        });
    }
}
