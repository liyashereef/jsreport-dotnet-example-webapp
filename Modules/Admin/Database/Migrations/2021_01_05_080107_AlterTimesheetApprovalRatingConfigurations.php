<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimesheetApprovalRatingConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timesheet_approval_rating_configurations', function (Blueprint $table) {
            $table->decimal('early',4,2)->nullable()->change();
            $table->decimal('untill',4,2)->nullable()->change();
            $table->decimal('difference',4,2)->nullable()->change();
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
            $table->decimal('early',2,1)->nullable()->change();
            $table->decimal('untill',2,1)->nullable()->change();
            $table->decimal('difference',2,1)->nullable()->change();
        });
    }
}
