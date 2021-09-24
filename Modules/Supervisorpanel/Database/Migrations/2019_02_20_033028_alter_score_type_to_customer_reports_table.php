<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterScoreTypeToCustomerReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // converting to decimal and then changing to double. wont work directly
        Schema::table('customer_reports', function (Blueprint $table) {
            $table->decimal('score',15,4)->change();
        });
        Schema::table('customer_reports', function (Blueprint $table) {
            $table->float('score',15,4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_reports', function (Blueprint $table) {
            $table->float('score',15,2)->change();
        });
    }
}
