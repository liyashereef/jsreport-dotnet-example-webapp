<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerRoomsAddSeverity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_rooms', function (Blueprint $table) {
            
            $table->integer('severity_id')->default('0')->unsigned()->after('name')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_rooms', function (Blueprint $table) {
        $table->dropColumn('severity_id');
    });
    }
}
