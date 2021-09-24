<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIncidentRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_recipients', function (Blueprint $table) {
            $table->boolean("amendment_notification")->default(0)->after("priority_id")->comment('0 -no amendment notification, 1- amendment notification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_recipients', function (Blueprint $table) {
            $table->dropColumn("amendment_notification");
        });
    }
}
