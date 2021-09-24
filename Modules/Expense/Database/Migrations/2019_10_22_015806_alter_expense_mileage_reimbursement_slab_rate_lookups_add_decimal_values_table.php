<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterExpenseMileageReimbursementSlabRateLookupsAddDecimalValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_mileage_reimbursement_slab_rates', function (Blueprint $table) {
            $table->decimal( 'cost',7,2 )->comment('Cost')->nullable()->change();
           });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_mileage_reimbursement_slab_rates', function (Blueprint $table) {
            $table->dropColumn('cost');
           
        });
    }
}
