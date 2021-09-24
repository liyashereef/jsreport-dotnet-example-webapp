<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisitedColumnToMSPfenceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_security_patrol_fence_datas', function (Blueprint $table) {
            $table->boolean('visited')->default(1)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_security_patrol_fence_datas', function (Blueprint $table) {
            $table->dropColumn('visited');
        });
    }
}
