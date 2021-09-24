<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkHourActivityCodeCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_hour_activity_code_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('work_hour_type_id')->comment('activity code');
            $table->unsignedInteger('customer_type_id');
            $table->string('code', 191);
            $table->string('description', 500);
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
        Schema::dropIfExists('work_hour_activity_code_customers');
    }
}
