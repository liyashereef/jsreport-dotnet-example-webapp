<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_claims', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->decimal('amount',10,2)->nullable();
            $table->integer('expense_gl_codes_id')->nullable();
            $table->string('description',250)->nullable();
            $table->integer('attachment')->default(0);
            $table->integer('attachment_id')->nullable();
            $table->string('no_attachment_reason',250)->nullable();
            $table->integer('billable')->default(0);
            $table->integer('project_id')->nullable();
            $table->integer('expense_category_id')->nullable();
            $table->integer('cost_center_id')->nullable();
            $table->boolean('reimbursed')->default(0);
            $table->integer('status_id');
            $table->integer('approved_by')->nullable();  
            $table->integer('financial_controller_id')->nullable();        
            $table->string('approver_comments',250)->nullable();  
            $table->string('finance_comments',250)->nullable();  
            $table->integer('payment_mode_id')->nullable();
            $table->boolean('claim_reimbursement')->default(0);
            $table->text('participants')->nullable();  
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('expense_claims');
    }
}
