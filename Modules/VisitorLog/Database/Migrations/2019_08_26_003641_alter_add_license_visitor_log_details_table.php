<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddLicenseVisitorLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_log_details', function (Blueprint $table) {
            $table->string('license_number', 20)->nullable()->after('signature_file_name');
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
            $table->dropColumn('license_number');
        });
    }
}
