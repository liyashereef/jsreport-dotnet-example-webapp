<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLandingPageModuleWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('landing_page_module_widgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40);
            $table->text('fields');
            $table->longText('icon')->nullable();
            $table->longText('view_permission')->nullable();
            $table->longText('api_url_path')->nullable();
            $table->longText('detail_url_path')->nullable();
            $table->boolean('api_type');
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
        Schema::dropIfExists('landing_page_module_widgets');
    }
}
