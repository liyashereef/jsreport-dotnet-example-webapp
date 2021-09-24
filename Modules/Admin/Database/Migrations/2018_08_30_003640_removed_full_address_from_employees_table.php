<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovedFullAddressFromEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('employee_full_address');
            $table->string('phone_ext', 255)->change();
            $table->string('employee_postal_code', 191)->nullable(false)->change();
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
            $table->string('employee_full_address', 191)->after('employee_address')->nullable();
            $table->string('phone_ext', 5)->change();
            $table->string('employee_postal_code', 191)->nullable()->change();
        });
    }
}
