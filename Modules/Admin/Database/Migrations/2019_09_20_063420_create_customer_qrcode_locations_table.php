<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerQrcodeLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('customer_qrcode_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->comment('customer id from customers')->nullable();
            $table->string('qrcode')->nullable();
            $table->string('location')->nullable();
            $table->string('no_of_attempts')->nullable();
            $table->integer('picture_enable_disable')->default('0')->comment('0-disabled,1-enabled');
            $table->integer('picture_mandatory')->default('0')->comment('0-no,1-yes');
            $table->integer('location_enable_disable')->default('0')->comment('0-disabled,1-enabled');
            $table->integer('qrcode_active')->default('0')->comment('0-deactivated,1-activated');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('customer_qrcode_locations');
    }
}
