<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
      
class AddColumnReviewCompletedToRecCandidates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')
            ->table('rec_candidates', function (Blueprint $table) {
                $table->boolean('review_completed')->default(0)
                ->after('reset_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidates', function (Blueprint $table) {
            $table->dropColumn('review_completed');
        });
    }
}
