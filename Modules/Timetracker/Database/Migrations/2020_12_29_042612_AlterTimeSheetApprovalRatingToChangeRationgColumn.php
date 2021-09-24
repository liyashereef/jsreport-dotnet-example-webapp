<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterTimeSheetApprovalRatingToChangeRationgColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->decimal('rating',4,2)->nullable()->change();
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
            $table->decimal('rating',4,2)->nullable()->change();
        });
    }
}
