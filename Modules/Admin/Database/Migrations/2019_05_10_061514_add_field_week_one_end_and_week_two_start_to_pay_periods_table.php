<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldWeekOneEndAndWeekTwoStartToPayPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_periods', function (Blueprint $table) {
            $table->date('week_one_end_date')->after('start_date')->nullable();
            $table->date('week_two_start_date')->after('week_one_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_periods', function (Blueprint $table) {
            $table->dropColumn('week_one_end_date');
            $table->dropColumn('week_two_start_date');


        });
    }
}
