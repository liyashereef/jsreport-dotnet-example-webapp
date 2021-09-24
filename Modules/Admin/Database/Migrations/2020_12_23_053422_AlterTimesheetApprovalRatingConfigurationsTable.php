<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimesheetApprovalRatingConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheet_approval_rating_configurations', function (Blueprint $table) {
            $table->integer('type')->nullable()->after('untill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timesheet_approval_rating_configurations', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
