<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_trackings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('completed_date')->nullable();
            $table->integer('candidate_id')->unsigned();
            $table->integer('process_lookups_id')->unsigned();
            $table->integer('process_tab_id')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->integer('entered_by')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_trackings');
    }
}
