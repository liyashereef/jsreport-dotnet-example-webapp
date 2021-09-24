<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');

            $table->unsignedInteger('initial_schedule_id')->default('0')->nullable();
            $table->decimal("week_1_total",7,2);
            $table->decimal("week_2_total",7,2);
            $table->decimal("biweekly_total",7,2);
            $table->boolean('status')->default(false)->nullable();
            $table->date('status_update_date')->nullable();
            $table->unsignedInteger('status_updated_by')->nullable();
            $table->mediumText('status_notes')->nullable();
            $table->unsignedInteger('pending_with')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('update_by');
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
        Schema::dropIfExists('employee_schedules');
    }
}
