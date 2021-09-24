<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTimeoffWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_timeoff_workflow', function (Blueprint $table) {
            $table->increments('id')->comment('Primary key');
            $table->integer('emp_role_id')->comment('role id of employee');
            $table->string('level')->comment('workflow');
            $table->integer('approver_role_id')->comment('role id of approver');
            $table->boolean('email_notification')->default(false)->comment('if email notification needed');
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
        Schema::dropIfExists('employee_timeoff_workflow');
    }
}
