<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToPaymentStatusFieldInPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('osgc_course_payment', function (Blueprint $table) {
            $table->integer('status')
            ->comment('NULL - default,0- Cancelled, 1- Success, 2- Processing')
            ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('osgc_course_payment', function (Blueprint $table) {
            $table->integer('status')
            ->change();
        });
    }
}
