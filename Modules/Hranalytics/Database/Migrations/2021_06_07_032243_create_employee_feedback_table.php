<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("customer_id");
            $table->string("subject");
            $table->mediumText("message");
            $table->unsignedInteger("department_id")->nullable();
            $table->unsignedInteger("status")->nullable();
            $table->float("latitude", 15, 7)->nullable();
            $table->float("longitude", 15, 7)->nullable();
            $table->unsignedInteger("created_by");
            $table->unsignedInteger("updated_by")->nullable();
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
        Schema::dropIfExists('employee_feedback');
    }
}
