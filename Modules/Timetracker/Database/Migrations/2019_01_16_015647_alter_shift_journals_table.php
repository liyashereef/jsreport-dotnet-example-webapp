<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterShiftJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_journals', function (Blueprint $table) {
            $table->integer('shift_id')->nullable()->change();
            $table->integer('customer_id')->nullable()->after('image');
            $table->integer('created_by')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_journals', function (Blueprint $table) {
            $table->integer('shift_id')->nullable(false)->change();
            $table->dropColumn('customer_id');
            $table->dropColumn('created_by');
        });
    }
}
