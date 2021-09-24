<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('expense_claim_id')->unsigned()->index('expense_log_expense_claim_id_foreign');
            $table->date('date');
            $table->decimal('amount',10,3);
            $table->boolean('reimbursed')->default(0);
            $table->integer('status_id');
            $table->integer('financial_status_id');
            $table->integer('updated_by');  
            $table->integer('financial_controller_id');        
            $table->integer('created_by');
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
        Schema::dropIfExists('expense_log');
    }
}
