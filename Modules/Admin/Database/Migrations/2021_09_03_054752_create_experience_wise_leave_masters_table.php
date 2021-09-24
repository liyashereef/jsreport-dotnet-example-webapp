<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperienceWiseLeaveMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_off_request_type_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('min_experience', 10, 2)->nullable();
            $table->decimal('no_of_leaves', 10, 2);
            $table->unsignedInteger('time_off_request_type_id');
            // $table->unsignedInteger("accrual_day")->default(1); //1 =Start of month
            // $table->unsignedInteger("accrual_month")->default(1); //1 =Which month
            $table->unsignedInteger("reset_term")->default(1); //1 =yearly
            $table->unsignedInteger("reset_month")->default(12); //1 =Start of month
            $table->unsignedInteger("reset_day")->default(1); //1 =End of month
            $table->boolean("carry_forward")->default(0);
            $table->unsignedInteger("carry_forward_percentage")->default(0);
            $table->unsignedInteger("carry_forward_expires_in_month")->nullable(); //In Month
            $table->unsignedInteger("encashment_percentage")->nullable(); //In Percentage
            $table->boolean('active')->default(1);
            $table->unsignedInteger("created_by")->nullable();
            $table->unsignedInteger("updated_by")->nullable();
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
        Schema::dropIfExists('time_off_request_type_settings');
    }
}
