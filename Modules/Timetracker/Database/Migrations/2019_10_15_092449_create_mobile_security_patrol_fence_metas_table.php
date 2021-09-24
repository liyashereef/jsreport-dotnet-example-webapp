<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSecurityPatrolFenceMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrol_fence_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id');
            $table->integer('total_visits');
            $table->integer('missed');
            $table->double('average');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_security_patrol_fence_metas');
    }
}
