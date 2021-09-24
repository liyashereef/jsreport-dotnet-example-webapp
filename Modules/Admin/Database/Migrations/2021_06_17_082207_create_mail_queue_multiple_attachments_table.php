<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailQueueMultipleAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_queue_multiple_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mail_queue_id')->nullable();
            $table->integer('attachment_id')->nullable();
            $table->string("s3_bucket_name")->nullable();
            $table->string("s3_repo_filename")->nullable(); 
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
        Schema::dropIfExists('mail_queue_multiple_attachments');
    }
}
