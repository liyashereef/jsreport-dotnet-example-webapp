<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCpidLookup extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('cpid_lookups', function (Blueprint $table) {
      $table->unsignedInteger('cpid_function_id')->nullable()->after('noc');
    });
  }


  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('cpid_lookups', function (Blueprint $table) {
      $table->dropColumn('cpid_function_id');
    });
  }
}
