<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMileageClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mileage_claims', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->nullable();
            $table->decimal('amount',10,2)->nullable();
            $table->string('description',250)->nullable();
            $table->string('starting_location',200)->nullable();
            $table->string('destination',200)->nullable();
            $table->float('starting_km')->comment('Starting odometer')->nullable();
            $table->float('ending_km')->comment('ending odometer')->nullable();
            $table->float('total_km')->comment('Total kilometer')->nullable();
            $table->boolean('vehicle_type')->default(0);
            $table->integer('vehicle_id')->nullable();
            $table->boolean('billable')->default(0);
            $table->boolean('associate_with_client')->default(0);
            $table->integer('project_id')->nullable();
            $table->integer('status_id');
            $table->integer('approved_by')->nullable();  
            $table->integer('financial_controller_id')->nullable();        
            $table->string('approver_comments',250)->nullable();  
            $table->string('finance_comments',250)->nullable();  
            $table->boolean('claim_reimbursement')->default(0);
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
        Schema::dropIfExists('mileage_claims');
    }
}
