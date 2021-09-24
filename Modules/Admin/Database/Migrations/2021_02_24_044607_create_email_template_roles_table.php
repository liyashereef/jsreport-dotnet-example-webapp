<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTemplateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('email_template_id');
            $table->unsignedInteger('role_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('email_template_roles', function (Blueprint $table) {
            $table->foreign('email_template_id')->references('id')->on('customer_template_emails')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_template_roles');
    }
}
