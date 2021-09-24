<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFederalBillingInIdsEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->smallInteger('is_federal_billing')->nullable()->after('is_canceled')->comment("NULL-default value, 0-No ,1- Yes");
            $table->decimal('balance_fee', 8, 2)->default(0)->after('given_rate');
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
            $table->dropColumn('is_federal_billing');
            $table->dropColumn('balance_fee');
        });
    }
}
