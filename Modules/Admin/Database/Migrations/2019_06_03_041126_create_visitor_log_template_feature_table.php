<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorLogTemplateFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_template_features', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('template_id')->unsigned()->index('visitor_log_template_features_template_id_foreign');
            $table->string('feature_name', 300);
            $table->string('feature_displayname', 300);
            $table->boolean('is_required')->nullable();
            $table->boolean('is_visible')->nullable();
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
        Schema::dropIfExists('visitor_log_template_features');
    }
}
