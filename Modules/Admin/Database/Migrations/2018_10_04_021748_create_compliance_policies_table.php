<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompliancePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_policies', function (Blueprint $table) {
            $table->increments('id');   
            $table->string('reference_code',255)->nullable()->comments('Auto generated code');
            $table->string('policy_name',250);  
            $table->integer('compliance_policy_category_id')->unsigned()->comments('ID from compliance_policy_categories');                      
            $table->string('policy_description',1000);
            $table->string('policy_objectives',1000);
            $table->string('policy_file',255)->nullable();
            $table->integer('status')->default('1')->comments('1-Active, 0-Inactive');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compliance_policies');
    }
}
