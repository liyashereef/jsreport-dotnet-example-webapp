<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_transaction_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->comment('entry id from ids entries table')->index();
            $table->integer('ids_online_payment_id')->unsigned()->nullable()->index();
            $table->integer('ids_payment_method_id')->nullable()->index();
            $table->decimal('amount', 8, 2)->default(0);
            $table->enum('transaction_type', ['Received', 'Refund'])->nullable()->index();
            $table->dateTime('refund_initiate_date')->nullable();
            $table->boolean('refund_completed')->comment('Null=Payment Received,0=Pending,1=Success')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

// to be paid by client

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ids_transaction_histories');
    }
}
