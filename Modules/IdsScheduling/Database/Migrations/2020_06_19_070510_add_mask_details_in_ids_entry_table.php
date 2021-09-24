<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaskDetailsInIdsEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->boolean('is_mask_given')->nullable()->after('payment_reason');
            $table->smallInteger('no_masks_given')->nullable()->after('is_mask_given');
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
            $table->dropColumn('is_mask_given');
            $table->dropColumn('no_masks_given');
        });
    }
}
