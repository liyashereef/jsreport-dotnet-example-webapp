<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMultipleTableCmufSubs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_contact_information', function (Blueprint $table) {
            $table->softDeletes();   
        });
        Schema::table('contracts_amendments', function (Blueprint $table) {
            $table->softDeletes();   
        });
        Schema::table('contracts_holiday_payment_agreements', function (Blueprint $table) {
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
        Schema::table('client_contact_information', function (Blueprint $table) {
            $table->dropSoftDeletes();   
        });
        Schema::table('contracts_amendments', function (Blueprint $table) {
            $table->dropSoftDeletes();   
        });
        Schema::table('contracts_holiday_payment_agreements', function (Blueprint $table) {
            $table->dropSoftDeletes();   
        });
    }
}
