<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorlogCustomerUserMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitorlog_customer_user_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('visitorlog_customer_user_mappings_user_id_foreign');
            $table->integer('customer_id')->unsigned()->index('visitorlog_customer_user_mappings_customer_id_foreign');
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
        Schema::dropIfExists('visitorlog_customer_user_mappings');
    }
}
