<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelRefundDetailsToIdsEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->boolean('is_refund_initiated')->nullable()->after('updated_by')->index();
            $table->integer('refund_initiated_by')->nullable()->after('is_refund_initiated')->index();
            $table->text('refund_note')->nullable()->after('refund_initiated_by');
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
            $table->dropColumn('is_refund_initiated');
            $table->dropColumn('refund_initiated_by');
            $table->dropColumn('refund_note');
        });
    }
}
