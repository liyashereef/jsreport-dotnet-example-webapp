<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePmTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pm_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->integer('assigned_to')->unsigned();
            $table->timestamp('due_date')->nullable();
            $table->integer('project_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('site_id')->unsigned()->nullable();
            $table->boolean('is_completed')->comment('0-not completed, 1-completed')->default(0);
            $table->integer('rating_id')->unsigned()->nullable();
            $table->integer('rated_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
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
        Schema::dropIfExists('pm_tasks');
    }
}
