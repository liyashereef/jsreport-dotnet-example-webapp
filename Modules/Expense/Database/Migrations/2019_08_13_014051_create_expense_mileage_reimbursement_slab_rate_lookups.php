<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseMileageReimbursementSlabRateLookups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_mileage_reimbursement_slab_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('starting_kilometer')->nullable();
            $table->integer('ending_kilometer')->nullable();
            $table->decimal('cost',7,3)->comment('Cost')->nullable();
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
        Schema::dropIfExists('expense_mileage_reimbursement_slab_rates');
    }
}
