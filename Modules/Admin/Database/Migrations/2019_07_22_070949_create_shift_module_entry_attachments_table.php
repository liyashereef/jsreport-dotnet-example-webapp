<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftModuleEntryAttachmentsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_module_entry_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_module_entry_id')->unsigned()->index('shift_module_entry_attachments_shift_module_entry_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('shift_module_entry_attachments_attachment_id_foreign');
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
        Schema::dropIfExists('shift_module_entry_attachments');
    }
}
