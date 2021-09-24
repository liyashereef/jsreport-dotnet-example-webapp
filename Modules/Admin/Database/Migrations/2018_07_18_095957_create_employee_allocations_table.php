<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAllocationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('employee_allocations', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('employee_allocations_user_id_foreign');
            $table->integer('supervisor_id')->unsigned()->index('employee_allocations_supervisor_id_foreign');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
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
    public function down() {
        Schema::drop('employee_allocations');
    }

}
