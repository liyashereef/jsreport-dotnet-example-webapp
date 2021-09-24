<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStcThresholdSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stc_threshold_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('no_of_days_critical')->default(2);
            $table->string('critical_days_color');
            $table->integer('no_of_days_major')->default(5);
            $table->string('major_days_color');
            $table->integer('no_of_days_minor')->default(5);
            $table->string('minor_days_color');
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
        Schema::dropIfExists('stc_threshold_settings');
    }
}
