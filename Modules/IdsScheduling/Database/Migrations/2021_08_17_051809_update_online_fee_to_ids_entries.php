<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOnlineFeeToIdsEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->decimal('online_processing_fee', 8, 2)->default(0)->after('balance_fee');
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
            $table->dropColumn('online_processing_fee');
        });
    }
}
