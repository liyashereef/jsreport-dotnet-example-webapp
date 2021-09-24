<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShiftModuleDropdownChangeOptionsInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_module_dropdown_options', function (Blueprint $table) {
            $table->string('option_info', 1000)->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_module_dropdown_options', function (Blueprint $table) {
            $table->string('option_info', 250)->nullable()->change();

        });
    }
}
