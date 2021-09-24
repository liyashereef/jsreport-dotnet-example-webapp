<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSequenceNumberToCapacityToolTaskFrequencyLookups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capacity_tool_task_frequency_lookups', function (Blueprint $table) {
            $table->integer('sequence_number')->nullable()->after('value');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capacity_tool_task_frequency_lookups', function (Blueprint $table) {
            $table->dropColumn('sequence_number');

        });
    }
}
