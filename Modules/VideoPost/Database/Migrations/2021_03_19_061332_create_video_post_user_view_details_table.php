<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoPostUserViewDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_post_user_view_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_post_id')->unsigned();
            $table->integer('viewed_user_id')->unsigned()->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('video_post_user_view_details', function (Blueprint $table) {
            $table->foreign('video_post_id')->references('id')->on('video_posts');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_post_user_view_details');
    }
}
