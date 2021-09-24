<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationDetailsOnDispatchRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            $table->tinyInteger('is_existing_customer')->after('dispatch_request_type_id')->default(1)->comment('1 = existing customer, 0= not a customer');
            $table->string('name')->after('customer_id')->comment('Name of non registered customer')->nullable();
            $table->decimal('latitude', 23, 20)->after('site_postalcode')->nullable();
            $table->decimal('longitude', 23, 20)->after('latitude')->nullable();

            $table->integer('customer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatch_requests', function (Blueprint $table) {
            $table->dropColumn('is_existing_customer');
            $table->dropColumn('name');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
