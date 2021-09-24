<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBalanceTransactionIdToIdsOnlinePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_online_payments', function (Blueprint $table) {
            $table->string('balance_transaction_id')->nullable()->after('payment_intent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_online_payments', function (Blueprint $table) {
            $table->dropColumn('balance_transaction_id');
        });
    }
}
