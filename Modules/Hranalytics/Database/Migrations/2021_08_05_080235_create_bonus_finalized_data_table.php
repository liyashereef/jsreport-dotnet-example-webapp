<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusFinalizedDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_finalized_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("bonus_pool_id");
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("no_of_shifts_taken");
            $table->unsignedInteger("no_of_calls_made");
            $table->decimal("average_wage", 15, 4)->nullable();
            $table->decimal("average_wage_gross_up", 15, 4)->nullable();
            $table->decimal("average_notice", 15, 4)->nullable();
            $table->decimal("average_notice_gross_up", 15, 4)->nullable();
            $table->decimal("reliability_score", 15, 4)->nullable();
            $table->decimal("total_adjustment", 15, 4)->nullable();
            $table->decimal("adjusted_bonus", 15, 4)->nullable();
            $table->decimal("unadjusted_bonus", 15, 4)->nullable();
            $table->unsignedInteger("created_by")->nullable();
            $table->unsignedInteger("rank")->nullable();
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
        Schema::dropIfExists('bonus_finalized_data');
    }
}
