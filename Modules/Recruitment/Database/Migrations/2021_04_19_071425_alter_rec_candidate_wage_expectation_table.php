<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidateWageExpectationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_wage_expectations', function (Blueprint $table) {
            $table->integer('wage_last_hours_per_week')
                ->nullable()
                ->after('wage_last_hourly');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_wage_expectations', function (Blueprint $table) {
            $table->dropColumn('wage_last_hours_per_week');
        });
    }
}
