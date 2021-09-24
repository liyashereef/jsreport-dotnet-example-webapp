<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIpCamerasTableToAddUniqueId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->string('unique_id')->nullable()->after('machine_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->dropColumn('unique_id');

        });
    }
}
