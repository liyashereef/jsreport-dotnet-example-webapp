<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerQrcodeSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_qrcode_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('expected_attempts')->default(0);
            $table->decimal('missed_count_percentage', 6, 2)->nullable();
            $table->text('qrcode_id')->nullable();
            $table->integer('shift_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_qrcode_summaries');
    }
}
