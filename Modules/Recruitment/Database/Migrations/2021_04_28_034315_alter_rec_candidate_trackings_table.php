<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::connection('mysql_rec')->table('rec_candidate_trackings', function (Blueprint $table) {
            $table->integer('job_id')
                ->nullable()
                ->after('process_tab_id');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_trackings', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });
    }
}
