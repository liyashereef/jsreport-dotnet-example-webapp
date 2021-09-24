<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailNotificationHelpersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('email_notification_helpers', function (Blueprint $table) {
            $table->string('email_notification_type_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('email_notification_helpers', function (Blueprint $table) {
           $table->string('email_notification_type_id')->nullable(false)->change();
        });
    }
}
