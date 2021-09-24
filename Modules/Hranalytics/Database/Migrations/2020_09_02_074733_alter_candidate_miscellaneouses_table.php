<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCandidateMiscellaneousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('candidate_miscellaneouses', function (Blueprint $table) {
             $table->enum('is_indian_native', ['No', 'Yes'])->nullable()->after('reason_for_release');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('candidate_miscellaneouses', function (Blueprint $table) {
            $table->dropColumn('is_indian_native');
         });
    }
}
