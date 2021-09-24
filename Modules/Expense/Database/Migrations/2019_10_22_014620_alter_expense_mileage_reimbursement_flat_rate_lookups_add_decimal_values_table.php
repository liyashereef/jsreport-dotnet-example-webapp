<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterExpenseMileageReimbursementFlatRateLookupsAddDecimalValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_mileage_reimbursement_flat_rates', function (Blueprint $table) {
                $table->decimal( 'flat_rate',7,2 )->comment('Flat Rate')->nullable()->change();
               });

             
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_mileage_reimbursement_flat_rates', function (Blueprint $table) {
            $table->dropColumn('flat_rate');
           
        });
    }
}
