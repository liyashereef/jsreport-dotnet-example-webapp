<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPassportPhotoServiceAvailabilityInIdsOfficeAndIdsService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_offices', function (Blueprint $table) {
            $table->boolean('is_photo_service')->default(false)->after('interval_valid_date');
        });

        Schema::table('ids_services', function (Blueprint $table) {
            $table->boolean('is_photo_service')->default(false)->after('description');
            $table->boolean('is_photo_service_required')->default(false)->after('is_photo_service');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_offices', function (Blueprint $table) {
            $table->dropColumn('is_photo_service');
        });
        Schema::table('ids_services', function (Blueprint $table) {
            $table->dropColumn('is_photo_service_required');
            $table->dropColumn('is_photo_service');
        });
    }
}
