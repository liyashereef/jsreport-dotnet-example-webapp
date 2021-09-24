<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractExpirySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_expiry_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alert_period_1')->unsigned();
            $table->integer('alert_period_2')->unsigned();
            $table->integer('alert_period_3')->unsigned();
            $table->time('email_1_time')->nullable();
            $table->time('email_2_time')->nullable();
            $table->time('email_3_time')->nullable();
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
        Schema::dropIfExists('contract_expiry_settings');
    }
}
