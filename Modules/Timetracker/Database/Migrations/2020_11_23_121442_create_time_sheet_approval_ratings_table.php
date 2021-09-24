<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeSheetApprovalRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_sheet_approval_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_shift_payperiod_id');
            $table->integer('payperiod_id');
            $table->integer('employee_id');
            $table->datetime('deadline_datetime');
            $table->integer('rating');
            $table->integer('latest_approved_by');
            $table->datetime('approved_datetime');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_sheet_approval_ratings');
    }
}
