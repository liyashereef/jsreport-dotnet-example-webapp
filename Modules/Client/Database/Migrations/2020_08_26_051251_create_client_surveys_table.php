<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("client_id");
            $table->string("client_contact_user");
            $table->unsignedInteger("rating");
            $table->mediumText("notes");
            $table->unsignedInteger("payperiod");
            $table->unsignedInteger("created_by");
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
        Schema::dropIfExists('client_surveys');
    }
}
