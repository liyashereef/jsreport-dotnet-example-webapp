<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnS3UrlToUofTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_rec')
            ->table('rec_candidate_force_certifications',
                function (Blueprint $table) {
                    $table->string('s3_location_path')->nullable()
                        ->after('attachment_id');

                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_rec')
            ->table('rec_candidate_force_certifications',
                function (Blueprint $table) {
                    $table->dropColumn('s3_location_path');
                });
    }
}
