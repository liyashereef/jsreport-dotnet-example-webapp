<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfpDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfp_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->nullable();
            $table->unsignedInteger('rfp_response_type_id');
            $table->unsignedInteger('employee_id');
            $table->string('rfp_site_name')->nullable();
            $table->string('rfp_site_address')->nullable();
            $table->string('rfp_site_city')->nullable();
            $table->string('rfp_site_postalcode')->nullable();
            $table->date('rfp_published_date')->nullable();
            $table->date('site_visit_deadline')->nullable();
            $table->date('qa_deadline')->nullable();
            $table->dateTime('submission_deadline')->nullable();
            $table->date('estimated_award_date')->nullable();
            $table->date('announcement_date')->nullable();
            $table->date('project_start_date')->nullable();
            $table->string('rfp_contact_name')->nullable();
            $table->string('rfp_contact_title')->nullable();
            $table->string('rfp_contact_address')->nullable();
            $table->string('rfp_phone_number')->nullable();
            $table->string('rfp_email')->nullable();
            $table->unsignedInteger('total_annual_hours')->nullable();
            $table->string('scope_summary')->nullable();
            $table->boolean('force_required')->default(0);
            $table->unsignedInteger('term')->nullable();
            $table->unsignedInteger('option_renewal')->nullable();
            $table->boolean('site_unionized')->default(0);
            $table->string('union_name')->nullable();
            $table->text('summary_notes')->nullable();
            $table->string('rpf_status')->default('Pending')->comment('Pending\Approved\Rejected');
            $table->integer('assign_resource_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfp_details');
    }
}
