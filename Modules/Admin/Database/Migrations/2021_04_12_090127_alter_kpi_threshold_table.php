<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKpiThresholdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpi_master_thresholds', function (Blueprint $table) {
            $table->decimal('min', 16, 2)->change();
            $table->decimal('max', 16, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpi_master_thresholds', function (Blueprint $table) {
            $table->integer('min')->change();
            $table->integer('max')->change();
        });
    }
}
