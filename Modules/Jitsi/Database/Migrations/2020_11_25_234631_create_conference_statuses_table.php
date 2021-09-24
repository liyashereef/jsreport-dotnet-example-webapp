<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConferenceStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conference_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('conferencecount')->default(0);
            $table->timestamps();
        });
        \DB::insert('insert into conference_statuses (id, conferencecount) values (?, ?)', [1, 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conference_statuses');
    }
}
