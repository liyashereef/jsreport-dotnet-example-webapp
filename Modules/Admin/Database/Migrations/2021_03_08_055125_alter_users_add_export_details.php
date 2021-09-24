<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddExportDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('users', function (Blueprint $table) {
            $table->integer('salutation_id')->nullable()->after('active');
            $table->integer('entity')->nullable()->after('salutation_id');
            $table->date('termination_date')->nullable()->after('entity');
            $table->enum('gender', [0,1])->comments('0-Male,1-Female')->nullable()->after('termination_date');
            $table->integer('marital_status_id')->nullable()->after('gender');
            $table->integer("sin")->nullable()->after('marital_status_id');
          });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salutation_id');
            $table->dropColumn('entity');
            $table->dropColumn('termination_date');
            $table->dropColumn('gender');
            $table->dropColumn('marital_status_id');
            $table->dropColumn('sin');
         });
    }
}
