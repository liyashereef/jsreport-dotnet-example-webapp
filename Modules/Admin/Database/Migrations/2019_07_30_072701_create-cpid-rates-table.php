<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpidRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpid_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned()->comment('cpid from cpid lookups');
            $table->date('effective_date')->nullable();
            $table->double('p_standard', 6, 2)->nullable();
            $table->double('p_overtime', 6, 2)->nullable();
            $table->double('p_holiday', 6, 2)->nullable();
            $table->double('b_standard', 6, 2)->nullable();
            $table->double('b_overtime', 6, 2)->nullable();
            $table->double('b_holiday', 6, 2)->nullable();
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
        Schema::dropIfExists('cpid_rates');
    }
}
