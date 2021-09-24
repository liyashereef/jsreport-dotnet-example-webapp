<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseTaxMasterLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_tax_master_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tax_master_id')->unsigned()->nullable();
            $table->decimal('tax_percentage', 6, 2)->nullable();
            $table->date('effective_from_date')->nullable();
            $table->date('effective_end_date')->comments('archived end date')->nullable();
            $table->integer('archived_by')->comments('auth id')->unsigned()->nullable();
            $table->boolean('status')->default(0)->comment('0=not archived, 1=archived');
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
        Schema::dropIfExists('expense_tax_master_logs');
    }
}
