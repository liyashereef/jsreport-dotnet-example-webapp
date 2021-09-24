<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("content_id");
            $table->string("attachment_title");
            $table->string("attachment_file");
            $table->unsignedInteger("attachment_type");
            $table->unsignedInteger("sequence");
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
        Schema::dropIfExists('content_attachments');
    }
}
