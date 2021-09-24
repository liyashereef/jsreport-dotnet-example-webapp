<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecCommissionairesUnderstandingLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_commissionaires_understanding_lookups', function (Blueprint $table) {
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
        Schema::connection('mysql_rec')->dropIfExists('rec_commissionaires_understanding_lookups');
    }
}
