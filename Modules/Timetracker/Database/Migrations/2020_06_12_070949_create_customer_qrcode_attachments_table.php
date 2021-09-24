<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerQrcodeAttachmentsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_qrcode_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qrcode_with_shift_id')->unsigned()->index('customer_qrcode_attachments_qrcode_with_shift_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('customer_qrcode_attachments_attachment_id_foreign');
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
        Schema::dropIfExists('customer_qrcode_attachments');
    }
}
