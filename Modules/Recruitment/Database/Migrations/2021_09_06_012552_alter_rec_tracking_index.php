<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecTrackingIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_trackings', function (Blueprint $table) {
            $table->index('candidate_id');
            $table->index('process_lookups_id');
            $table->index('process_tab_id');
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
            $table->dropIndex('rec_candidate_trackings_candidate_id_index');
            $table->dropIndex('rec_candidate_trackings_process_lookups_id_index');
            $table->dropIndex('rec_candidate_trackings_process_tab_id_index');
           });
    }
}
