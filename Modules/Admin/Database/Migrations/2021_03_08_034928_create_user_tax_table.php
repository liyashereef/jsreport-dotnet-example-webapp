<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tax', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id")->nullable();
            $table->string("federal_td1_claim")->nullable();
            $table->string("provincial_td1_claim")->nullable();
            $table->boolean("is_cpp_exempt")->nullable();
            $table->boolean("is_uic_exempt")->nullable();
            $table->string("tax_province")->nullable();
            $table->string("epaystub_email")->nullable();
            $table->boolean('is_epaystub_exempt')->nullable();
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
        Schema::dropIfExists('user_tax');
    }
}
