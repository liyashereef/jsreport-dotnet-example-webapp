<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToIncidentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incident_attachments', function (Blueprint $table) {
            $table->foreign('incident_id')->references('id')->on('incident_reports')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('attachment_id')->references('id')->on('attachments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incident_attachments', function (Blueprint $table) {
            $table->dropForeign('incident_attachments_incident_id_foreign');
            $table->dropForeign('incident_attachments_attachment_id_foreign');
        });
    }
}
