<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentExpiryColorSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_expiry_color_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("grace_period_in_days");
            $table->string("grace_period_color_code");
            $table->unsignedInteger("alert_period_in_days");
            $table->string("alert_period_color_code");
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
        Schema::dropIfExists('document_expiry_color_settings');
    }
}
