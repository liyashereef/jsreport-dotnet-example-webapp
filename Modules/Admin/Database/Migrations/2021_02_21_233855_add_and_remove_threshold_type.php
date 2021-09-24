<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAndRemoveThresholdType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpi_masters', function (Blueprint $table) {
            $table->enum('threshold_type', [1,2])->comments('1-Rating,2-Percentage')->after('machine_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_masters', function (Blueprint $table) {
            $table->dropColumn('threshold_type');
        });
    }
}
