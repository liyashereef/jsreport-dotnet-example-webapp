<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnIndianstatusToAboriginaldescentToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_users', function (Blueprint $table) {
            $table->boolean('indian_status')->comment('Aboriginal descent status')->change();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_users', function (Blueprint $table) {
            $table->boolean('indian_status')->comment('')->change();
        });
    }
}
