<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->integer('smart_phone_type_id')->unsigned()->nullable()->after('geo_location_long');
            $table->string('smart_phone_skill_level', 255)->nullable()->after('geo_location_long');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('smart_phone_type_id');
            $table->dropColumn('smart_phone_skill_level');
        });
    }
}
