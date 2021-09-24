<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWhistleblowerStatusLookupsTableToAddInitialStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whistleblower_status_lookups', function (Blueprint $table) {
            $table->boolean('inital_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whistleblower_status_lookups', function (Blueprint $table) {
            $table->dropColumn('inital_status');
          });
    }
}
