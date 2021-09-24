<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractTemplateCondition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmufs', function (Blueprint $table) {
            $table->unsignedInteger('contractonourtemplate')->after('contract_annualincrease_allowed')->nullable();
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
            $table->dropColumn('contractonourtemplate');
            
        });
    }
}
