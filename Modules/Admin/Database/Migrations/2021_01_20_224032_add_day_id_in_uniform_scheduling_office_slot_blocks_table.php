<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayIdInUniformSchedulingOfficeSlotBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_scheduling_office_slot_blocks', function (Blueprint $table) {
            $table->integer('day_id')->nullable()->after('uniform_scheduling_office_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_scheduling_office_slot_blocks', function (Blueprint $table) {
            $table->dropColumn('day_id');
        });
    }
}
