<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLastWagePerHourToCandidateWageExpection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_wage_expectations', function (Blueprint $table) {
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
        Schema::table('', function (Blueprint $table) {
            $table->dropColumn('wage_last_hours_per_week');
        });
    }
}
