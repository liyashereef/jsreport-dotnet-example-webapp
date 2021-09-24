<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnResetTokenToRecCandidates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')
            ->table('rec_candidates', function (Blueprint $table) {
            $table->string('reset_token', 100)
                ->nullable()
                ->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')->table('rec_candidates', function (Blueprint $table) {
            $table->dropColumn('reset_token');
        });
    }
}
