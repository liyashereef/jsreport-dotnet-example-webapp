<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidatesAddAttachmentPdfId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::connection('mysql_rec')
            ->table('rec_candidates', function (Blueprint $table) {
                $table->integer('attachment_pdf_id')
                ->nullable()
                ->after('review_completed');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidates', function (Blueprint $table) {
            $table->dropColumn('attachment_pdf_id');
        });
    }
}
