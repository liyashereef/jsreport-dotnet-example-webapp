<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOpenshiftAddReadflag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('candidate_openshift_applications', function (Blueprint $table) {
            $table->boolean("readflag")->default(0)->after("longitude");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_openshift_applications', function (Blueprint $table) {
            $table->dropColumn("readflag");
        });
    }
}
