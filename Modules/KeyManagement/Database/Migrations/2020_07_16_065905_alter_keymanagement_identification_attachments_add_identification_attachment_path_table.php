<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKeymanagementIdentificationAttachmentsAddIdentificationAttachmentPathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keymanagement_identification_attachments', function (Blueprint $table) {
            $table->string('identification_attachment_path', 255)->nullable()->after('identification_attachment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keymanagement_identification_attachments', function (Blueprint $table) {
            $table->string('identification_attachment_path', 255)->nullable()->after('identification_attachment_id');
        });
    }
}
