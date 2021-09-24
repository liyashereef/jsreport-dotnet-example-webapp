<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCGLMeetRoomScheduledMeetingRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->unsignedInteger("scheduleid")->default(0)->after("employee_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conference_sessions', function (Blueprint $table) {
            $table->dropColumn("scheduleid");
        });
    }
}
