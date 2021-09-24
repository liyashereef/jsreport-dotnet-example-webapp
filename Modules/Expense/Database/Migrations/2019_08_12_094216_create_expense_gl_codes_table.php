<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseGlCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_gl_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gl_code',250);
            $table->string('short_name',250);
            $table->string('description',1000)->nullable();
            $table->string('grouping',250)->nullable();
            $table->string('pnl_subcode',250)->nullable();
            $table->string('pnl_item',250)->nullable();
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
        Schema::dropIfExists('expense_gl_codes');
    }
}
