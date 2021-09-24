<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFieldsNullableTimeSheetApprovalRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->integer('latest_approved_by')->nullable()->change();
            $table->datetime('approved_datetime')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->integer('latest_approved_by')->nullable()->change();
            $table->datetime('approved_datetime')->nullable()->change();
        });
    }
}
