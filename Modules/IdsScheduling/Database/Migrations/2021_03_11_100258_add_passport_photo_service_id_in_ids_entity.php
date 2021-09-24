<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPassportPhotoServiceIdInIdsEntity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->integer('passport_photo_service_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->dropColumn('passport_photo_service_id');
        });
    }
}
