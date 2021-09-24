<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomersAddEmployeeEnableKeyManagementSignatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('employee_key_management_signature')->default(false)->after('employee_key_management');
            $table->boolean('employee_key_management_image_id')->default(false)->after('employee_key_management_signature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('employee_key_management_signature');
        $table->dropColumn('employee_key_management_image_id');
    }
}
