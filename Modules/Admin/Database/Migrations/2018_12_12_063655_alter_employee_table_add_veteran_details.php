<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeTableAddVeteranDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('employees', function (Blueprint $table) {
            $table->string('vet_service_number')->nullable()->after('employee_vet_status');
            $table->date('vet_enrollment_date')->nullable()->after('employee_vet_status');
            $table->date('vet_release_date')->nullable()->after('employee_vet_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('employees', function ($table) {
            $table->dropColumn('vet_service_number');
            $table->dropColumn('vet_enrollment_date');
            $table->dropColumn('vet_release_date');
        });
    }
}
