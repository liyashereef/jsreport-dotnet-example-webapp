<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsOnlineRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_online_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->comment('entry id from ids entries table')->index();
            $table->integer('user_id')->nullable()->index();
            $table->integer('ids_online_payment_id')->unsigned()->nullable()->index();
            $table->string('ids_online_refund_id')->nullable()->index();
            $table->string('ids_online_charge_id')->nullable()->index();
            $table->string('balance_transaction_id')->nullable()->index();
            $table->decimal('amount', 8, 2)->default(0);
            $table->smallInteger('refund_status')->comment('1=Refund Initiated, 2=Refund Success, 3=Refund Failed')->nullable();
            $table->dateTime('refund_start_time')->nullable();
            $table->dateTime('refund_end_time')->nullable();
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
        Schema::dropIfExists('ids_online_refunds');
    }
}
