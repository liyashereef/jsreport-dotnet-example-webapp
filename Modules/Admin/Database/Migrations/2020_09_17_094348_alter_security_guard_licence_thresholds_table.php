<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSecurityGuardLicenceThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('security_guard_licence_thresholds', function (Blueprint $table) {
            $table->integer('threshold')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('security_guard_licence_thresholds', function (Blueprint $table) {
            $table->date('threshold')->change();
        });
    }
}
