<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractBillingRateChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_billing_rate_changes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ratechangetitle');
            $table->boolean('status');
            $table->unsignedInteger('createdby');
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
        Schema::dropIfExists('contract_billing_rate_changes');
    }
}
