<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrPatrolToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('qr_picture_limit')->nullable()->after('qr_patrol_enabled');
            $table->boolean('qr_interval_check')->default(false)->after('qr_picture_limit');
            $table->string('qr_duration', 191)->nullable()->after('qr_interval_check');
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
            $table->dropColumn('qr_picture_limit');
            $table->dropColumn('qr_interval_check');
            $table->dropColumn('qr_duration');
        });
    }
}
