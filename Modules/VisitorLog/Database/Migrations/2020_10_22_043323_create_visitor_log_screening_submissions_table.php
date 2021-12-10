<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorLogScreeningSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_screening_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_log_screening_template_id')->nullable(false);
            $table->integer('customer_id')->nullable(false);
            $table->string('uid')->nullable();
            $table->boolean('passed')->nullable();
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
        Schema::dropIfExists('visitor_log_screening_submissions');
    }
}
