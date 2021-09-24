<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientShowUpAndPaymentLookupFieldsInIdsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->smallInteger('is_client_show_up')->nullable()->after('given_rate');
            $table->integer('ids_payment_reason_id')->nullable()->after('ids_payment_method_id');
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
            $table->dropColumn('is_client_show_up');
            $table->dropColumn('ids_payment_reason_id');
        });
    }
}
