<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShiftModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_modules', function (Blueprint $table) {
            $table->boolean('dashboard_view')->default(0)->after('is_active');
            $table->boolean('enable_timeshift')->default(0)->after('dashboard_view');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_modules', function (Blueprint $table) {
            $table->dropColumn('dashboard_view');
            $table->dropColumn('enable_timeshift');
        });
    }
}
