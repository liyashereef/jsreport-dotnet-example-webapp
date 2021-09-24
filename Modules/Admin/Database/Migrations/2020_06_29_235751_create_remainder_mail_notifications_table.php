<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemainderMailNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('remainder_mail_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('notification_type', 250);
            $table->string('model', 250);
            $table->integer('document_id')->unsigned();
            $table->integer('user_id')->unsigned()->index('remainder_mail_notifications_user_id_foreign');
            $table->date('expiry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('remainder_mail_notifications');
    }

}
