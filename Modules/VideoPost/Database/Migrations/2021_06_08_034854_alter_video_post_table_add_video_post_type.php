<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVideoPostTableAddVideoPostType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_posts', function (Blueprint $table) {
            $table->enum('type', ['summary', 'detailed'])
                ->default('summary')
                ->after('description');
            $table->enum('file_type', ['video','pdf'])
                ->default('video')
                ->after('file_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_posts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('file_type');
        });
    }
}
