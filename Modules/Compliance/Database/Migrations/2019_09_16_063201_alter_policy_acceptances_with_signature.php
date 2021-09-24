<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPolicyAcceptancesWithSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('policy_acceptances',function(Blueprint $table){
            $table->string('signature_file_name', 300)->after('comment')->nullable();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('policy_acceptances',function(Blueprint $table){

            $table->dropColumn('signature_file_name');

        });
    }
}
