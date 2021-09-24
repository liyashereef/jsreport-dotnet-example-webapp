<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRefundDetailsInIdsTransactionHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_transaction_histories', function (Blueprint $table) {
            $table->dropColumn('refund_initiate_date');
            $table->dropColumn('refund_completed');

            $table->integer('user_id')->nullable()->after('entry_id')->index();
            $table->text('refund_note')->nullable()->after('transaction_type');
            $table->smallInteger('refund_status')->nullable()->after('refund_note')
            ->comment('0= No Refund, 1 = Refund Requested, 2= Refund Approved, 3= Refund Rejected');
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
            $table->dropColumn('user_id');
            $table->dropColumn('refund_note');
            $table->dropColumn('refund_status');

            $table->dateTime('refund_initiate_date')->nullable();
            $table->boolean('refund_completed')->comment('Null=Payment Received,0=Pending,1=Success')->nullable();
        });
    }
}
