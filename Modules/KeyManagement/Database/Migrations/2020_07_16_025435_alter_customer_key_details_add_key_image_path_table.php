<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerKeyDetailsAddKeyImagePathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_key_details', function (Blueprint $table) {
            $table->string('key_image_path', 255)->nullable()->after('attachment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_key_details', function (Blueprint $table) {
            $table->string('key_image_path', 255)->nullable()->after('attachment_id');
        });
    }
}
