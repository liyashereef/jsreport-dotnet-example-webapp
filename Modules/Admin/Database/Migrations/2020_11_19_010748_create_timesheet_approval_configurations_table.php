<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesheetApprovalConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheet_approval_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('day')->nullable();
            $table->time("time")->nullable();
            $table->integer('email_1_time')->unsigned()->nullable();
            $table->integer('email_2_time')->unsigned()->nullable();
            $table->integer('email_3_time')->unsigned()->nullable();
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
        Schema::table('timesheet_approval_configurations', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('day');
            $table->dropColumn('time');
            $table->dropColumn('email_1_time');
            $table->dropColumn('email_2_time');
            $table->dropColumn('email_3_time');

        });
    }
}
