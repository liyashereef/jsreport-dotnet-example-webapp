<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseMileageReimbursementFlatRateLookups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_mileage_reimbursement_flat_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('flat_rate',7,3)->comment('Flat Rate')->nullable();
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
        Schema::dropIfExists('expense_mileage_reimbursement_flat_rates');
    }
}
