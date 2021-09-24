<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRfpDetailsWinLosesWithOfferedbytheclientnoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfp_details_win_loses', function($table) {
            $table->text('offered_by_the_client_no')->after('did_we_take_it_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfp_details_win_loses', function($table) {
            $table->dropColumn('offered_by_the_client_no');
        });
    }
}
