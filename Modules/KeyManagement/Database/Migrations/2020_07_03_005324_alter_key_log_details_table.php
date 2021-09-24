<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKeyLogDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_log_details', function (Blueprint $table) {
            $table->string('checked_in_by', 255)->nullable()->after('checked_out_to');
            $table->string('checked_in_from', 255)->nullable()->after('checked_in_by');
            $table->integer('check_in_signature_attachment_id')->nullable()->after('signature_attachment_id');
            $table->string('check_in_notes', 255)->nullable()->after('notes');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('key_log_details', function (Blueprint $table) {
            $table->dropColumn('checked_in_by');
            $table->dropColumn('checked_in_from');
            $table->dropColumn('check_in_signature_attachment_id');
            $table->dropColumn('updated_by');
        });
    }
}
