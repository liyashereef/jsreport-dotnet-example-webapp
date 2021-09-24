<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('notification_types', 'email_notification_types');
        Schema::table('email_notification_types', function (Blueprint $table) {
            $table->string('display_name')->after('type');
            $table->boolean('customer_based')->default(1)->after('display_name');
            $table->boolean('requester_based')->default(0)->after('customer_based');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_notification_types', function (Blueprint $table) {
            $table->dropColumn('display_name');
            $table->dropColumn('customer_based');
            $table->dropColumn('requester_based');

        });
    }
}
