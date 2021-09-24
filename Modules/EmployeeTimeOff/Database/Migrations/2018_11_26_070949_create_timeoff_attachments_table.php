<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeoffAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeoff_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timeoff_id')->unsigned()->index('timeoff_attachments_timeoff_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('timeoff_attachments_attachment_id_foreign');
            $table->integer('created_by')->unsigned();
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
        Schema::dropIfExists('timeoff_attachments');
    }
}
