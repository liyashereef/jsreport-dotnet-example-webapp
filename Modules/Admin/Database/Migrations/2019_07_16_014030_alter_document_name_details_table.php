<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentNameDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_name_details', function (Blueprint $table) {
            $table->string('answer_type', 255)->nullable()->after('document_category_id');
            $table->integer('other_category_name_id')->nullable()->after('answer_type');
            $table->integer('is_valid')->nullable()->after('is_editable');
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
            $table->dropColumn('answer_type');
            $table->dropColumn('other_category_name_id');
            $table->dropColumn('is_valid');
        });
    }
}
