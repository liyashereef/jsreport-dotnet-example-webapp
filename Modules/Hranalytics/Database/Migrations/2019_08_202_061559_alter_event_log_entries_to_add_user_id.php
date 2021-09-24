<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterEventLogEntriesToAddUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_log_entries', function ($table) {
            $table->integer('candidate_id')->unsigned()->nullable()->change();
            $table->integer('user_id')->unsigned()->index('event_log_entries_user_id_foreign')->after('candidate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_log_entries', function ($table) {

            $table->dropColumn('user_id');
            $table->integer('candidate_id')->unsigned()->change();

        });
    }
}
