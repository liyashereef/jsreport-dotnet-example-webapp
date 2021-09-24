<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompliancePoliciesTableToAddFieldEnableAgreeDisagree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('compliance_policies',function(Blueprint $table){
            $table->boolean('enable_agree_or_disagree')->default(0)->after('policy_file');
            $table->boolean('enable_agree_textbox')->default(0)->after('enable_agree_or_disagree');
            $table->boolean('enable_disagree_textbox')->default(0)->after('enable_agree_textbox');

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
        Schema::table('compliance_policies',function(Blueprint $table){

            $table->dropColumn('enable_agree_or_disagree');
            $table->dropColumn('enable_agree_textbox');
            $table->dropColumn('enable_disagree_textbox');
        });
    }
}
