<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRfpTrackingStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_tracking_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rfp_details_id')->nullable();
            $table->integer('rfp_process_steps_id')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('entered_by_id')->nullable();
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
        Schema::dropIfExists('rfp_tracking_stages');
    }
}
