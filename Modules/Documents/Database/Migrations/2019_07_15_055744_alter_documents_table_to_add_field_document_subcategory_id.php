<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentsTableToAddFieldDocumentSubcategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('other_category_lookup_id')->nullable()->after('user_id');
            $table->integer('other_category_name_id')->nullable()->after('document_category_id');
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('other_category_lookup_id');
            $table->dropColumn('other_category_name_id');
        });
    }
}
