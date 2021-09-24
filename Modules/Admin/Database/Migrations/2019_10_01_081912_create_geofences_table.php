<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeofencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_fences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('address',500)->nullable();
            $table->unsignedInteger('customer_id');
            $table->decimal('geo_lat',15,10);
            $table->decimal('geo_lon',15,10);
            $table->decimal('geo_rad',15,2);
            $table->unsignedInteger('visit_count')->default(0);
            $table->unsignedInteger('created_by');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('geo_fences');
    }
}
