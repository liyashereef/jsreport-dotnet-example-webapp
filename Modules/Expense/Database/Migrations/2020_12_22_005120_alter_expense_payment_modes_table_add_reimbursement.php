<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterExpensePaymentModesTableAddReimbursement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_payment_modes', function (Blueprint $table) {
            $table->boolean('reimbursement')->nullable()->after('mode_of_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_payment_modes', function (Blueprint $table) {
            $table->dropColumn('reimbursement');
        });
    }
}
