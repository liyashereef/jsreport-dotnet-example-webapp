<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDropdownIdToShiftModulePostOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_module_post_orders', function (Blueprint $table) {
            $table->integer('dropdown_id')->unsigned()->after('field_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_module_post_orders', function (Blueprint $table) {
            $table->dropColumn('dropdown_id');
        });
    }
}
