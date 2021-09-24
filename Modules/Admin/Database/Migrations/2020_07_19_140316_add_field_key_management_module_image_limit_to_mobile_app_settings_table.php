<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldKeyManagementModuleImageLimitToMobileAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_app_settings', function (Blueprint $table) {
            $table->integer('key_management_module_image_limit')->nullable()->after('shift_module_image_limit')->comment('for keymanagement module');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_app_settings', function (Blueprint $table) {
            $table->dropColumn('key_management_module_image_limit');
        });
    }
}
