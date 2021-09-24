<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateSecurityGuardingExperincesToAddSecurityClearance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->enum('security_clearance', ['Yes', 'No'])->nullable()->after('expiry_cpr');
            $table->text('security_clearance_type')->nullable()->after('security_clearance');
            $table->date('security_clearance_expiry_date')->nullable()->after('security_clearance_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_security_guarding_experinces', function (Blueprint $table) {
            $table->dropColumn('security_clearance');
            $table->dropColumn('security_clearance_type');
            $table->dropColumn('security_clearance_expiry_date');
        });
    }
}
