<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsAmendmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts_amendments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id')->default(0);
            $table->mediumText('amendment_description')->nullable();
            $table->unsignedInteger('amendment_attachment_id')->nullable()->default(0);
            $table->unsignedInteger('created_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts_amendments');
    }
}
