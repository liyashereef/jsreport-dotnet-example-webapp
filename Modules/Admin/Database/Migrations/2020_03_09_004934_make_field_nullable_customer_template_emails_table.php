<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFieldNullableCustomerTemplateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::table('customer_template_emails', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->nullable()->change();
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
            $table->unsignedInteger('customer_id')->nullable(false)->change();
        });
    }
}
