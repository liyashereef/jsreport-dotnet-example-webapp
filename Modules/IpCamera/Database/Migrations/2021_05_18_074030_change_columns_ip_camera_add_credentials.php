<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsIpCameraAddCredentials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->dropColumn('nod_mac')->after('name');
            $table->dropColumn('pan_mac')->after('nod_mac');
            $table->dropColumn('gateway_mac')->after('pan_mac');
            $table->dropColumn('latest_detection_at')->after('online_updated_at');
        });

        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->string('credentials',200)->nullable()->after('name');
            $table->string('ip_port',300)->nullable()->after('credentials');
            $table->string('port',200)->nullable()->after('ip_port');
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
            $table->dropColumn('port');
            $table->dropColumn('ip_port');
            $table->dropColumn('credentials');
        });

        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->string('nod_mac', 200)->nullable()->after('name');;
            $table->string('pan_mac', 200)->nullable()->after('nod_mac');;
            $table->string('gateway_mac', 200)->nullable()->after('pan_mac');;
            $table->dateTime('latest_detection_at')->nullable()->after('online_updated_at');;
        });
    }
}
