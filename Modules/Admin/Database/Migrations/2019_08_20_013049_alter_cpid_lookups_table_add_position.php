<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpidLookupsTableAddPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cpid_lookups', function(Blueprint $table){
            $table->integer('position_id')->unsigned()->comment('position id from position lookups')->after('cpid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cpid_lookups', function(Blueprint $table){
             $table->dropColumn('position_id');
        });
    }
}
