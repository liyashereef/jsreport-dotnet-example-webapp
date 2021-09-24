<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HoursOffToDecimalCustomerReportAdhoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_report_adhocs', function (Blueprint $table) {
            $table->float('hours_off', 6, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_report_adhocs', function (Blueprint $table) {
            $table->integer('hours_off')->change();
        });
    }
}
