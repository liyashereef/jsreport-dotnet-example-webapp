<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUuidAndForceCheckoutFieldsOnVisitorLogDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->string('uuid')->nullable()->after('created_by');
            $table->boolean('force_checkout')->default(0)->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('force_checkout');
        });
    }
}
