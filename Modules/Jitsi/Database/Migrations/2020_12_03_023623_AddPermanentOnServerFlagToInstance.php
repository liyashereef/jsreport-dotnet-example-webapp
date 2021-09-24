<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermanentOnServerFlagToInstance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conference_recording_servers', function (Blueprint $table) {
            $table->boolean("permanentonserver")->after("ip")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conference_recording_servers', function (Blueprint $table) {
            $table->dropColumn('permanentonserver');
        });
        
    }
}
