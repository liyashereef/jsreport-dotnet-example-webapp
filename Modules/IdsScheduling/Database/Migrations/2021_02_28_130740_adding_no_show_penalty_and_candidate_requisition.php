<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingNoShowPenaltyAndCandidateRequisition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->decimal('cancellation_penalty', 8, 2)->default(0)->after('given_rate');
            $table->integer('cancelled_booking_id')->nullable()->after('cancellation_penalty');
            $table->boolean('is_candidate')->default(0)->after('cancelled_booking_id');
            $table->string('candidate_requisition_no')->nullable()->after('is_candidate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ids_entries', function (Blueprint $table) {
            $table->dropColumn('cancellation_penalty');
            $table->dropColumn('cancelled_booking_id');
            $table->dropColumn('is_candidate');
            $table->dropColumn('candidate_requisition_no');
        });
    }
}
