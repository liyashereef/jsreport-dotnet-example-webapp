<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdsEntryAmountSplitUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ids_entry_amount_split_ups', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type')->nullable()->comment("null = cancellation-penalty, 0 = tax, 1 = services, 2 = passport-photo");
            $table->integer('entry_id')->comment("ids's of ids_entries");
            $table->integer('service_id')->nullable()->comment("ids's of ids_services and ids_passport_photo_services");
            $table->decimal('rate', 8, 2)->default(0);
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
        Schema::dropIfExists('ids_entry_amount_split_ups');
    }
}
