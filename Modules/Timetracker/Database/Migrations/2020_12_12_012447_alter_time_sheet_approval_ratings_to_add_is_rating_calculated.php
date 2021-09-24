<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimeSheetApprovalRatingsToAddIsRatingCalculated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->boolean('is_rating_calculated')->default(false)->after('approved_datetime');
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
            $table->dropColumn('is_rating_calculated');
        });
    }
}
