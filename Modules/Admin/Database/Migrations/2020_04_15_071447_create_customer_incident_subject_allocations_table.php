<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerIncidentSubjectAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('customer_incident_subject_allocations', function (Blueprint $table) {
             $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('subject_id');
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
          Schema::dropIfExists('customer_incident_subject_allocations');
    }
}
