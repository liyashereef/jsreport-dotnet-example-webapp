<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentNameDetailsTableToAddFieldIsAutoArchive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_name_details', function (Blueprint $table) {
           
            $table->integer('is_auto_archive')->nullable()->after('is_valid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_name_details', function (Blueprint $table) {
            $table->dropColumn('is_auto_archive');
        });
    }
}
