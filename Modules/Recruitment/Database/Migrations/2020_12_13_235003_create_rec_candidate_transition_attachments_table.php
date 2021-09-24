<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCandidateTransitionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_transition_attachments', function (Blueprint $table) {
             $table->increments('id');
            $table->integer('transition_id')->unsigned()->index('rec_candidate_transition_attachments_transition_id_foreign');
            $table->integer('attachment_id')->unsigned()->index('rec_candidate_transition_attachments_attachment_id_foreign');
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_transition_attachments');
    }
}
