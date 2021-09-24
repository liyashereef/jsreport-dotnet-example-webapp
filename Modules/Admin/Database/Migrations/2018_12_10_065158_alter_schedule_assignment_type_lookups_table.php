<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScheduleAssignmentTypeLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_assignment_type_lookups', function (Blueprint $table) {
            $table->string('is_deletable')->after('type')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_assignment_type_lookups', function (Blueprint $table) {
            $table->dropColumn('is_deletable');
        });
    }
}
