<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterExpenseMileageReimbursementFlatRateLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_mileage_reimbursement_flat_rates', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->after('flat_rate');
             $table->boolean('is_active')->default(1)->after('user_id');
             
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
            $table->dropColumn('user_id');
            $table->dropColumn('is_active');
        });
    }
}
