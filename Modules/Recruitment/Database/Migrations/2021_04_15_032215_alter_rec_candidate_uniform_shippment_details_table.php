<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecCandidateUniformShippmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_uniform_shippment_details', function (Blueprint $table) {
            $table->integer('kit_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidate_uniform_shippment_details', function (Blueprint $table) {
             $table->integer('kit_id')->nullable(false)->change();
        });
    }
}
