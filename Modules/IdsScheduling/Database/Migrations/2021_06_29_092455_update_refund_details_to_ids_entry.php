<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRefundDetailsToIdsEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->dropColumn('is_refund_initiated');
            $table->dropColumn('refund_note');

            $table->dateTime('refund_initiated_date')->nullable()->after('refund_initiated_by')->index();
            $table->integer('refund_completed_by')->nullable()->after('refund_initiated_date')->index();
            $table->dateTime('refund_completed_date')->nullable()->after('refund_completed_by')->index();
            $table->smallInteger('refund_status')->nullable()->after('refund_completed_date')
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
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->dropColumn('refund_initiated_date');
            $table->dropColumn('refund_completed_by');
            $table->dropColumn('refund_completed_date');
            $table->dropColumn('refund_status');
            $table->boolean('is_refund_initiated')->nullable()->after('updated_by')->index();
            $table->text('refund_note')->nullable()->after('refund_initiated_by');
        });
    }
}
