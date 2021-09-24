<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUraTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ura_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->comment('Transaction for *');
            $table->unsignedInteger('created_by')->comment('Who initiated the transaction');

            $table->unsignedInteger('employee_shift_payperiod_id')->nullable();
            $table->unsignedInteger('employee_shift_report_entry_id')->nullable();

            $table->enum('transaction_type', ['DEBIT', 'CREDIT']);
            $table->unsignedInteger('ura_operation_id');

            $table->unsignedInteger('ura_rate_id')->nullable();
            $table->double('hours', 8, 2)->default(0);

            $table->boolean('revoked')->default(false)->comment('Transaction is reverted');
            $table->double('amount', 8, 2);
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('ura_transactions');
    }
}
