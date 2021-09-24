<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionairesUnderstandingLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissionaires_understanding_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->text('commissionaires_understandings');
            $table->string('short_name')->nullable();
            $table->integer('order_sequence');
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
        Schema::dropIfExists('commissionaires_understanding_lookups');
    }
}
