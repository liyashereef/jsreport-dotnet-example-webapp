<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeOffRolesbasedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_off_request_type_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("timeoff_request_type_setting_id");
            $table->unsignedInteger("role_id")->nullable();
            $table->boolean("role_exception")->default(false);
            $table->unsignedInteger("created_by")->nullable();
            $table->unsignedInteger("updated_by")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_off_request_type_roles');
    }
}
