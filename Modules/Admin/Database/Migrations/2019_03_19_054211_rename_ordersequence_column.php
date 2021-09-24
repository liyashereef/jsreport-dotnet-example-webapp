<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOrdersequenceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_experience_lookups', function (Blueprint $table) {
            $table->renameColumn('order_sequence', 'score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_experience_lookups', function (Blueprint $table) {
            $table->renameColumn('score', 'order_sequence');
        });
    }
}
