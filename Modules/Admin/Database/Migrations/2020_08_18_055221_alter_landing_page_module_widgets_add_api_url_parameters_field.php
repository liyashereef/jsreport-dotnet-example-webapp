<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLandingPageModuleWidgetsAddApiUrlParametersField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_page_module_widgets', function (Blueprint $table) {
            $table->string('api_url_parameters')->nullable()->after('api_url_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_page_module_widgets', function (Blueprint $table) {
            $table->dropColumn('api_url_parameters');
        });
    }
}
