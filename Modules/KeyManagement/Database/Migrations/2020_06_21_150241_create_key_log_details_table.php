<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_log_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_key_detail_id')->nullable();
            $table->string('checked_out_by',500)->nullable();
            $table->string('checked_out_to',500)->nullable();
            $table->string('company_name',500)->nullable();
            $table->integer('identification_id')->nullable();
            $table->integer('identification_attachment_id')->nullable();
            $table->integer('signature_attachment_id')->nullable();
            $table->integer('key_availablity_id')->nullable();
            $table->string('notes',500)->nullable();
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
        Schema::dropIfExists('key_log_details');
    }
}
