<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientConcernTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_concerns', function (Blueprint $table) {
            $table->unsignedInteger('status_lookup_id')->nullable()->after('concern');
            $table->string('reg_manager_notes', 1000)->nullable()->after('status_lookup_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_concerns', function ($table) {
            $table->dropColumn('status_lookup_id');
            $table->dropColumn('reg_manager_notes');
        });
    }
}
