<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingPaymentMethodToIdsEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->smallInteger('is_payment_received')->nullable()->after('given_rate');
            $table->integer('ids_payment_method_id')->nullable()->after('is_payment_received');
            $table->text('payment_reason')->nullable()->after('ids_payment_method_id');
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
            $table->dropColumn('is_payment_received');
            $table->dropColumn('ids_payment_method_id');
            $table->dropColumn('payment_reason');
         });
    }
}
