<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientContactInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_contact_information', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('primary_contact');
            $table->string('contact_name');
            $table->unsignedInteger('contact_jobtitle');
            $table->string('contact_emailaddress');
            $table->string('contact_phoneno');
            $table->string('contact_cellno');
            $table->string('contact_faxno')->nullable();
            $table->unsignedInteger('contractid');
            $table->boolean('status')->default(1);



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_contact_information');
    }
}
