<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecOnboardingDocumentAttachmentsTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_onboarding_document_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned()->index('document_id');
            $table->string('file_name',250)->nullable();  
            $table->integer('file_type')->default('0')->comments('1-video, 2-doc'); 
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
        Schema::connection('mysql_rec')->dropIfExists('rec_onboarding_document_attachments');
    }
}
