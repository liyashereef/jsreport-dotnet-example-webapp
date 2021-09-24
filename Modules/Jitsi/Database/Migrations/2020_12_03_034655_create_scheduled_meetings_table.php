<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduledMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->string("title");
            $table->datetime("startdate");
            $table->datetime("enddate");
            $table->float("meetinghours", 10, 2)->default(0);
            $table->boolean("status")->default(false);
            $table->unsignedInteger("createdby")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduled_meetings');
    }
}
