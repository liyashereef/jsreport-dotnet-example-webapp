<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateSecurityClearanceTableToAddStatusExpiryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('candidate_security_clearances',function($table){

            $table->date('status_expiry_date')->nullable()->after('candidate_id');
            $table->boolean('renew_status')->nullable()->after('status_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('candidate_security_clearances',function($table){

            $table->dropColumn('status_expiry_date');
            $table->dropColumn('renew_status');
        });
    }
}
