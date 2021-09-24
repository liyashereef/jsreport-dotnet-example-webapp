<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDraftToIdsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->smallInteger('is_online_payment_received')->default(2)->after('is_canceled')->comment("0-Payment not received,1- Online payment received, 2-Offline payment will receive");
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
            $table->dropColumn('is_online_payment_received');
        });
    }
}
