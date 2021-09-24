<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateOpenshiftApplicationsDatatype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_openshift_applications', function (Blueprint $table) {
      
            $table->string('starttime')->change();
            $table->string('endtime')->change();
            $table->unsignedInteger('multifillid')->after('customerid')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_openshift_applications', function (Blueprint $table) {
            $table->string('starttime')->change();
            $table->string('endtime')->change();
            //$table->dropColumn('multifillid');
        });
    }
}
