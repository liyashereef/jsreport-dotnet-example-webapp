<?php

use Illuminate\Database\Migrations\Migration;

class AlterEventLogTableChangeAccptedRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        \DB::statement("alter table event_log_entries change accepted_rate accepted_rate double;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("alter table event_log_entries change accepted_rate accepted_rate int(11);");
    }
}
