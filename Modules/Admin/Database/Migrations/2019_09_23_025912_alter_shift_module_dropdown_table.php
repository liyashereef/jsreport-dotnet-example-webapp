<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShiftModuleDropdownTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_module_dropdowns', function (Blueprint $table) {
            $table->integer('info')->nullable()->after('dropdown_name');
            $table->string('detail')->nullable()->after('info');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_module_dropdowns', function (Blueprint $table) {
            $table->dropColumn('info');
            $table->dropColumn('detail');
        });
    }
}
