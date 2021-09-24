<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdToUserRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->integer('customer_id')->unsigned()->nullable()->after('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
}
