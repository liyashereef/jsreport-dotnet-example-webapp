<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameShiftJournalColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('shift_journal_enabled', 'guard_tour_enabled');
            $table->renameColumn('shift_journal_duration', 'guard_tour_duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('guard_tour_enabled', 'shift_journal_enabled');
            $table->renameColumn('guard_tour_duration', 'shift_journal_duration');
        });
    }
}
