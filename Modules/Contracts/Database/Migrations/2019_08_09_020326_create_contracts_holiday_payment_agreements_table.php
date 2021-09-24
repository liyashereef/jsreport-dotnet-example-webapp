<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsHolidayPaymentAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts_holiday_payment_agreements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contract_id')->default(0);
            $table->unsignedInteger('holiday_id')->default(0);
            $table->unsignedInteger('paymentstatus_id')->default(0);
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
        Schema::dropIfExists('contracts_holiday_payment_agreements');
    }
}
