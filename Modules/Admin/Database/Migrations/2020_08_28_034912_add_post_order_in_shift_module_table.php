<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostOrderInShiftModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_modules', function (Blueprint $table) {
            $table->boolean('post_order')->default(0)->after('enable_timeshift');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_modules', function (Blueprint $table) {
            $table->dropColumn('post_order');
        });
    }
}
