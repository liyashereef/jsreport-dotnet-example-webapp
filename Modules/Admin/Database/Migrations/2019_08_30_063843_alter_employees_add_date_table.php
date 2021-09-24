<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeesAddDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('years_of_security')->after('active')->nullable();
            $table->date('being_canada_since')->after('years_of_security')->nullable();
            $table->double('wage_expectations_from', 15,4)->after('being_canada_since')->nullable();
            $table->double('wage_expectations_to', 15,4)->after('wage_expectations_from')->nullable();
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('years_of_security');
            $table->dropColumn('being_canada_since');
            $table->dropColumn('wage_expectations_from');
            $table->dropColumn('wage_expectations_to');
         });
    }
}
