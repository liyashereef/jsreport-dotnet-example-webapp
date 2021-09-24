<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimeOffRequestTypeTableAddEditableField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_off_request_type_lookups', function (Blueprint $table) {
            $table->boolean('is_editable')->after('is_deletable')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_off_request_type_lookups', function (Blueprint $table) {
            $table->dropColumn('is_editable');
        });
    }
}
