<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimeOffRequestTypeTableAddColorField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('time_off_request_type_lookups', function (Blueprint $table) {
            $table->string('color')->nullable()->after('request_type');
            $table->boolean('is_deletable')->after('color')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('time_off_request_type_lookups', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->dropColumn('is_deletable');
        });
    }
}
