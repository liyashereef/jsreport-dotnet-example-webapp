<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateSecurityGuardingExperienceTableToAddSin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('candidate_security_guarding_experinces',function($table){

            $table->string('social_insurance_number')->nullable()->after('positions_experinces');
            $table->boolean('sin_expiry_date_status')->nullable()->after('social_insurance_number');
            $table->date('sin_expiry_date')->nullable()->after('sin_expiry_date_status');
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
        Schema::table('candidate_security_guarding_experinces',function($table){

            $table->dropColumn('social_insurance_number');
            $table->dropColumn('sin_expiry_date_status');
            $table->dropColumn('sin_expiry_date');
        });
    }
}
