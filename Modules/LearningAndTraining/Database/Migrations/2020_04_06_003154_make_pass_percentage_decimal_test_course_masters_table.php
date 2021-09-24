<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePassPercentageDecimalTestCourseMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_course_masters', function (Blueprint $table) {
            $table->decimal('pass_percentage',15,2)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('test_course_masters', function (Blueprint $table) {
            $table->Integer('pass_percentage')->change();
         
        });
    }
}


