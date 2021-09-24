<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddShiftStartDateToShiftModuleEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_module_entries', function (Blueprint $table) {
            $table->dateTime('shift_start_date')->after('shift_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_module_entries', function (Blueprint $table) {
            $table->dropColumn('shift_start_date');
        });
    }
}
