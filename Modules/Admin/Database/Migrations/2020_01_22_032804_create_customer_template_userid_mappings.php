<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTemplateUseridMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('customer_template_userid_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_email_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->softDeletes();
 
            
        });
         Schema::table('customer_template_userid_mappings', function (Blueprint $table) {
           $table->foreign('template_email_id')->references('id')->on('customer_template_emails')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_template_userid_mappings');
    }
}
