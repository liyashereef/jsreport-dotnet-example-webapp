<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsConvertedRecCandidatesTable extends Migration
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
                $table->boolean('is_converted')
                ->default(false)
                ->after('review_completed');
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
            $table->dropColumn('is_converted');
         });
    }
}
