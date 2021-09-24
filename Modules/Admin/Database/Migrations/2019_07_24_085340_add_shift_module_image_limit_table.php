<?php

use Illuminate\Database\Migrations\Migration;

class AddShiftModuleImageLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_app_settings', function ($table) {
            $table->integer('shift_module_image_limit')->nullable()->after('trip_show_distance')->comment('for shift module');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_app_settings', function ($table) {
            $table->dropColumn('shift_module_image_limit');
        });
    }
}
