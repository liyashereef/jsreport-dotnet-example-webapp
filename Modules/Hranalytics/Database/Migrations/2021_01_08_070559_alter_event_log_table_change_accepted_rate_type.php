<?php

use Illuminate\Database\Migrations\Migration;

class AlterEventLogTableChangeAcceptedRateType extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {

        \DB::statement("alter table event_log_entries change accepted_rate accepted_rate int(11);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("alter table event_log_entries change accepted_rate accepted_rate varchar(20);");
    }
}
