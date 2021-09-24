<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorLogTemplateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_log_template_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->unsigned()->index('visitor_log_template_fields_mappings_template_id_foreign');
            $table->string('fieldname', 255)->nullable();
            $table->string('field_displayname', 255)->nullable();
            $table->integer('field_type')->nullable();
            $table->boolean('is_required')->nullable();
            $table->boolean('is_visible')->nullable();
            $table->boolean('is_custom')->nullable();
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
        Schema::dropIfExists('visitor_log_template_fields');
    }
}
