<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftStartTimeToShiftJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_journals', function (Blueprint $table) {
            $table->dateTime('shift_start_time')->after('submitted_time')->nullable();
            $table->boolean('shift_submitted')->default(0)->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_journals', function (Blueprint $table) {
            $table->dropColumn('shift_start_time');
            $table->dropColumn('shift_submitted');
        });
    }
}
