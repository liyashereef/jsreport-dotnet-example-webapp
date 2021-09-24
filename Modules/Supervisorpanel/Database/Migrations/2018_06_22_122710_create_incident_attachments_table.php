<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncidentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('incident_id')->unsigned()->index('incident_attachments_incident_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('incident_attachments_attachment_id_foreign');
            $table->string('short_description', 260)->nullable();
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
        Schema::dropIfExists('incident_attachments');
    }
}
