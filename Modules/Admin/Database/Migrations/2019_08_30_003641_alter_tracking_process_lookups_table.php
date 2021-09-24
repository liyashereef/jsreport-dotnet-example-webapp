<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrackingProcessLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_process_lookups', function (Blueprint $table) {
            $table->integer('step_number')->nullable()->after('process_steps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_process_lookups', function (Blueprint $table) {
            $table->dropColumn('step_number');
        });
    }
}
