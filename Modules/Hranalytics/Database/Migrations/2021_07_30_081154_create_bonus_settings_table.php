<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->date("start_date");
            $table->date("end_date");
            $table->decimal("bonus_amount", 15, 2);
            $table->decimal("wagecap_percentage", 5, 2);
            $table->decimal("shiftcap_percentage", 5, 2);
            $table->decimal("noticecap_percentage", 5, 2);
            $table->unsignedInteger("created_by");
            $table->unsignedInteger("updated_by")->nullable();
            $table->unsignedInteger("noofrun")->default(0);
            $table->boolean("active")->default(false);
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
        Schema::dropIfExists('bonus_settings');
    }
}
