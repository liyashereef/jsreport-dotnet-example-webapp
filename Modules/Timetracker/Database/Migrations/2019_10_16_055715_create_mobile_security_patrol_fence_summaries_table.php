<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileSecurityPatrolFenceSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_security_patrol_fence_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_id');
            $table->integer('fence_id');
            $table->integer('visit_count_expected');
            $table->integer('visit_count_actual');
            $table->integer('visit_count_missed');
            $table->double('visit_count_average',6,2);
            $table->integer('hours_total')->comments('In seconds');
            $table->double('hours_average',9,3)->comments('In seconds');
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
        Schema::dropIfExists('mobile_security_patrol_fence_summaries');
    }
}
