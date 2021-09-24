<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPolicyAcceptanceTableToAddFieldAgreeDisagreeReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('policy_acceptances',function(Blueprint $table){

            $table->integer('agree')->comment('agree=1,disagree=0')->after('employee_id');
            $table->integer('compliance_policy_agree_disagree_reason_id')->after('agree')->nullable();
            $table->text('comment')->after('compliance_policy_agree_disagree_reason_id')->nullable();

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
        Schema::table('policy_acceptances',function(Blueprint $table){

            $table->dropColumn('agree');
            $table->dropColumn('compliance_policy_agree_disagree_reason_id');
            $table->dropColumn('comment');

        });
    }
}
