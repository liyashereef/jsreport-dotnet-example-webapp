<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfpDetailsWinLosesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_details_win_loses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rfp_details_id')->nullable();
            $table->string('status')->default('Pending')->comment('Pending/Win/Lose');
            $table->string('rfp_debrief_attended')->comment('Yes/No')->nullable();
            $table->text('rfp_debrief_attended_no')->nullable();
            $table->string('did_we_take_it')->comment('Yes/No')->nullable();
            $table->text('did_we_take_it_no')->nullable();
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
        Schema::dropIfExists('rfp_details_win_loses');
    }
}
