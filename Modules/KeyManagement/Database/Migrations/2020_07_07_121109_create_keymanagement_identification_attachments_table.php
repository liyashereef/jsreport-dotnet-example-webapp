<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeymanagementIdentificationAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keymanagement_identification_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('key_log_detail_id')->nullable();
            $table->integer('identification_id')->nullable();
            $table->integer('identification_attachment_id')->nullable();
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
        Schema::dropIfExists('keymanagement_identification_attachments');
    }
}
