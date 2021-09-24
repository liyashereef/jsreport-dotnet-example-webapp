<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTimesheetApprovalConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheet_approval_configurations', function (Blueprint $table) {
            $table->decimal('email_1_time',8,2)->nullable()->change();
            $table->decimal('email_2_time',8,2)->nullable()->change();
            $table->decimal('email_3_time',8,2)->nullable()->change();
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
            $table->integer('email_1_time')->unsigned()->nullable()->change();
            $table->integer('email_2_time')->unsigned()->nullable()->change();
            $table->integer('email_3_time')->unsigned()->nullable()->change();
        });
    }
}
