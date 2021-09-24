<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerIncidentSubjectAllocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_incident_subject_allocations', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->after('subject_id');
            $table->integer('priority_id')->unsigned()->after('category_id');
            $table->integer('incident_response_time')->nullable()->after('priority_id');
            $table->text('sop')->nullable()->after('incident_response_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_incident_subject_allocations', function (Blueprint $table) {
            $table->dropColumn('incident_category_id');
            $table->dropColumn('priority_id');
            $table->dropColumn('subject_response_time');
            $table->dropColumn('sop');
        });
    }
}
