<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterEventLogEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_log_entries', function ($table) {
            $table->integer('multiple_shift_id')->nullable()->after('schedule_customer_requirement_id');
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

            $table->dropColumn('multiple_shift_id');

        });
    }
}
