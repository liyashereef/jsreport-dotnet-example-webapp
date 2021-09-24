<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailAccountsMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_accounts_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->text('display_name')->nullable();
            $table->string('email_address')->nullable();
            $table->string('user_name')->nullable();
            $table->string('password')->nullable();
            $table->string('smtp_server')->nullable();
            $table->string('port')->nullable();
            $table->string('encryption')->nullable();
            $table->boolean('default')->default(0);
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
        Schema::dropIfExists('email_accounts_masters');
    }
}
