<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerTemplateEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_template_emails', function (Blueprint $table) {
            $table->boolean('role_based')->default(false)->after('send_to_supervisors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_template_emails', function (Blueprint $table) {
            $table->dropColumn('role_based');
        });
    }
}
