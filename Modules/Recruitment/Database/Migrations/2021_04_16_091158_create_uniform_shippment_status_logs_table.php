<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformShippmentStatusLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')->create('rec_candidate_uniform_shippment_details_status_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rec_candidate_uniform_shippment_details_id');
            $table->integer('status');
            $table->timestamp('datetime')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::connection('mysql_rec')->dropIfExists('rec_candidate_uniform_shippment_details_status_logs');
    }
}
