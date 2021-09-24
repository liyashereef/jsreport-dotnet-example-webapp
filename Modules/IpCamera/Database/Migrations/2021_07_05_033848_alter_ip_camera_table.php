<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIpCameraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('ip_cameras', function (Blueprint $table) {
            $table->renameColumn('credentials', 'credential_username');
            $table->renameColumn('port', 'controller_port');
            $table->renameColumn('ip_port', 'ip');
         });
          Schema::table('ip_cameras', function (Blueprint $table) {
            $table->string('credential_password', 200)->nullable()->after('credential_username');
            $table->string('rtsp_port', 300)->nullable()->after('ip');
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
            $table->renameColumn('credential_username', 'credentials');
             $table->renameColumn('controller_port', 'port');
             $table->renameColumn('ip', 'ip_port');
        });
        Schema::table('ip_cameras', function (Blueprint $table) {
            $table->dropColumn('credential_password');
            $table->dropColumn('rtsp_port');
        });
    }
}
