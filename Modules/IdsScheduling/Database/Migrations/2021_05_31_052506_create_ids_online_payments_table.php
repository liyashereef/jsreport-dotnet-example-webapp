<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsOnlinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_online_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entry_id')->unsigned()->comment('entry id from ids entries table')->index();
            $table->decimal('amount', 8, 2)->default(0);
            $table->string('transaction_id');
            $table->string('payment_intent')->index();
            $table->string('email')->nullable();
            $table->integer('status')->nullable()->comment('default- NULL,0-Failed,1-Success,2-processing');;
            $table->dateTime('started_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('entry_id_updated_at')->nullable();
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
        Schema::dropIfExists('ids_online_payments');
    }
}
