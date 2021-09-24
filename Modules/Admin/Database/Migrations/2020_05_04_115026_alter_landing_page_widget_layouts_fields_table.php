<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLandingPageWidgetLayoutsFieldsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('landing_page_widget_layouts', function (Blueprint $table) {
            $table->double('width_to_adjust_with_active_side_menu', 2)->nullable()->after('no_of_columns');
            $table->double('width_to_adjust_without_active_side_menu', 2)->nullable()->after('width_to_adjust_with_active_side_menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('landing_page_widget_layouts', function (Blueprint $table) {
            $table->dropColumn('width_to_adjust_with_active_side_menu');
            $table->dropColumn('width_to_adjust_without_active_side_menu');
        });
    }

}
