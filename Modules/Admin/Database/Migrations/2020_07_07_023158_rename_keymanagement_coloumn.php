<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameKeymanagementColoumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('employee_key_management', 'key_management_enabled');
            $table->renameColumn('employee_key_management_signature', 'key_management_signature');
            $table->renameColumn('employee_key_management_image_id', 'key_management_image_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('key_management_enabled', 'employee_key_management');
            $table->renameColumn('key_management_signature', 'employee_key_management_signature');
            $table->renameColumn('key_management_image_id', 'employee_key_management_image_id');
        });
    }
}
