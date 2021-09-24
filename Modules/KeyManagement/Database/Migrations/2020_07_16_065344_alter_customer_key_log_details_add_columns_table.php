<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerKeyLogDetailsAddColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('key_log_details', function (Blueprint $table) {
            $table->string('check_out_signature_path', 255)->nullable()->after('signature_attachment_id');
            $table->string('check_in_signature_path', 255)->nullable()->after('check_in_signature_attachment_id');
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
            $table->string('check_out_signature_path', 255)->nullable()->after('signature_attachment_id');
            $table->string('check_in_signature_path', 255)->nullable()->after('check_in_signature_attachment_id');
        });
    }
}
