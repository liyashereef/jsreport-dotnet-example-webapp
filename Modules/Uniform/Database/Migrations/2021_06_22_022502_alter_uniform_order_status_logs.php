<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUniformOrderStatusLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniform_order_status_logs', function (Blueprint $table) {
            $table->boolean('is_email_required')->after('notes')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniform_order_status_logs', function (Blueprint $table) {
            $table->dropColumn('is_email_required');
        });
    }
}
