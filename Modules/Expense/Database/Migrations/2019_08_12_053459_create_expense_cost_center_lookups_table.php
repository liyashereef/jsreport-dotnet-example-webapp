<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseCostCenterLookupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_cost_center_lookups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('center_number', 255);
            $table->integer('center_owner_id');
            $table->integer('center_senior_manager_id');
            $table->integer('region_id');
            $table->string('description', 255)->nullable();
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
        Schema::dropIfExists('expense_cost_center_lookups');
    }
}
