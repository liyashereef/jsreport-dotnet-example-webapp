<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompliancePolicyAgreeDisagreeReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliance_policy_agree_disagree_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('compliance_policy_id')->nullable();
            $table->boolean('agree_or_disagree')->comment('agree=1,disagree=0')->default(1);
            $table->string('reason')->nullable();
            $table->integer('created_by');
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
        Schema::dropIfExists('compliance_policy_agree_disagree_reasons');
    }
}
