<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_job_details_status_logs', function (Blueprint $table) {
            $table->index('rec_job_details_id');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_match_scores', function (Blueprint $table) {
            $table->index('candidate_id');
            $table->index('job_id');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_attachments', function (Blueprint $table) {
            $table->index('candidate_id');
            $table->index('attachment_id');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_availabilities', function (Blueprint $table) {
            $table->index('candidate_id');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_documents', function (Blueprint $table) {
            $table->index('candidate_id');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_screening_personality_scores', function (Blueprint $table) {
            $table->index('candidate_id');
        }); 

        Schema::connection('mysql_rec')->table('rec_candidate_uniform_calculated', function (Blueprint $table) {
            $table->index('candidate_id');
            $table->index('kit_id');
        });     

        Schema::connection('mysql_rec')->table('rec_candidate_uniform_shippment_details', function (Blueprint $table) {
            $table->index('candidate_id');
            $table->index('kit_id');
        }); 

        Schema::connection('mysql_rec')->table('rec_job_document_allocations', function (Blueprint $table) {
            $table->index('document_id');
            $table->index('job_id');
        }); 

        Schema::connection('mysql_rec')->table('rec_onboarding_document_attachments', function (Blueprint $table) {
            $table->index('document_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_job_details_status_logs', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_job_details_status_logs_rec_job_details_id_index');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_match_scores', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_match_scores_candidate_id_index');
            $table->dropIndex('rec_candidate_match_scores_job_id_index');

        });

        Schema::connection('mysql_rec')->table('rec_candidate_attachments', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_attachments_candidate_id_index');
            $table->dropIndex('rec_candidate_attachments_attachment_id_index');
        });

        Schema::connection('mysql_rec')->table('rec_candidate_availabilities', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_availabilities_candidate_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_candidate_documents', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_documents_candidate_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_candidate_screening_personality_scores', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_screening_personality_scores_candidate_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_candidate_uniform_calculated', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_uniform_calculated_candidate_id_index');
            $table->dropIndex('rec_candidate_uniform_calculated_kit_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_candidate_uniform_shippment_details', function (Blueprint $table) {
            $table->dropIndex('rec_candidate_uniform_shippment_details_candidate_id_index');
            $table->dropIndex('rec_candidate_uniform_shippment_details_kit_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_job_document_allocations', function (Blueprint $table) {
            $table->dropIndex('rec_job_document_allocations_document_id_index');
            $table->dropIndex('rec_job_document_allocations_job_id_index');
        }); 

        Schema::connection('mysql_rec')->table('rec_onboarding_document_attachments', function (Blueprint $table) {
            $table->dropIndex('rec_onboarding_document_attachments_document_id_index');
        }); 
    }
}
