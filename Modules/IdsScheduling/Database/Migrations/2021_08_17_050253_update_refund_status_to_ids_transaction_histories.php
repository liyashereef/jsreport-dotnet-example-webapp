<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRefundStatusToIdsTransactionHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_transaction_histories', function (Blueprint $table) {
            \DB::statement("ALTER TABLE ids_transaction_histories CHANGE `refund_status` `refund_status` SMALLINT(6)  NULL comment '0= No Refund, 1 = Refund Requested, 2= Refund Approved, 3= Refund Rejected, 4= Refund Initiated Stripe, 5=Refund Pending, 6=Refund Failed';");
            $table->integer('online_refund_id')->nullable()->after('ids_payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_transaction_histories', function (Blueprint $table) {
            \DB::statement("ALTER TABLE ids_transaction_histories CHANGE `refund_status` `refund_status` SMALLINT(6)  NULL comment '0= No Refund, 1 = Refund Requested, 2= Refund Approved, 3= Refund Rejected';");
              $table->dropColumn('online_refund_id');
        });
    }
}
