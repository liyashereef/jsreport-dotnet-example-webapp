<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetApprovalRatingConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheet_approval_rating_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timesheet_approval_configurations_id')->nullable();
            $table->integer('early')->nullable();
            $table->integer('untill')->nullable();
            $table->integer('difference')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
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
            $table->dropColumn('id');
            $table->dropColumn('timesheet_approval_configurations_id');
            $table->dropColumn('early');
            $table->dropColumn('untill');
            $table->dropColumn('difference');
            $table->dropColumn('rating');
        });
    }
}
