<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUniformSchedulingOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniform_scheduling_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('adress')->nullable();
            $table->decimal('latitude',15,10)->nullable();
            $table->decimal('longitude',15,10)->nullable();
            $table->string('phone_number_ext')->nullable();
            $table->string('phone_number')->nullable();
            $table->time('office_start_time')->nullable();
            $table->time('office_end_time')->nullable();
            $table->text('special_instructions')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::dropIfExists('uniform_scheduling_offices');
    }
}
