<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRfpDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfp_details', function (Blueprint $table) {
            $table->boolean('site_visit_available')
                ->nullable()
                ->after('rfp_published_date');
            $table->boolean('q_a_deadline_available')
                ->nullable()
                ->after('site_visit_deadline');
            $table->boolean('rfp_contact_title_available')
                ->nullable()
                ->after('rfp_contact_name');
            $table->boolean('rfp_contact_address_available')
                ->nullable()
                ->after('rfp_contact_title');
            $table->boolean('rfp_phone_number_available')
                ->nullable()
                ->after('rfp_contact_address');
            $table->boolean('rfp_email_available')
                ->nullable()
                ->after('rfp_phone_number');
            $table->dropColumn(['estimated_award_date']);
            $table->string('scope_summary', 1100)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfp_details', function (Blueprint $table) {
            $table->string('scope_summary', 1100)->change();
            $table->date('estimated_award_date')->nullable();
            $table->dropColumn([
                'rfp_email_available',
                'rfp_phone_number_available',
                'rfp_contact_address_available',
                'rfp_contact_title_available',
                'q_a_deadline_available',
                'site_visit_available',
            ]);
        });
    }
}
