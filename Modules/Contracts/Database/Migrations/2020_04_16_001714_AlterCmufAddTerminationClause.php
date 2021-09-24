<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCmufAddTerminationClause extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->boolean('termination_clause_client')->after('contract_length_renewal_years')->default(false);
            $table->integer('terminationnoticeperiodclient')->after('termination_clause_client')->default(0)->nullable();
            $table->boolean('termination_clause')->after('terminationnoticeperiodclient')->default(false);
            $table->integer('terminationnoticeperiod')->after('termination_clause')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->dropColumn('termination_clause_client');
            $table->dropColumn('terminationnoticeperiodclient');
            $table->dropColumn('termination_clause');
            $table->dropColumn('terminationnoticeperiod');
        });
    }
}
