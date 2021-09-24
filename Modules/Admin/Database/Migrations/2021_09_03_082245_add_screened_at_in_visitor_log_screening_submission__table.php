<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScreenedAtInVisitorLogScreeningSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_screening_submissions', function (Blueprint $table) {
            $table->dateTime('screened_at')->nullable()->after('passed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_screening_submissions', function (Blueprint $table) {
            $table->dropColumn('screened_at');
        });
    }
}
