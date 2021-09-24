<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduledMeetingReferenceToMeeting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conference_rooms', function (Blueprint $table) {
            $table->unsignedInteger("scheduleroomid")->after("room_password")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conference_rooms', function (Blueprint $table) {
            $table->dropColumn("scheduleroomid");
        });
    }
}
