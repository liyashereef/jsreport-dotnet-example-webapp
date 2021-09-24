<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfpDetails extends Model
{
    use SoftDeletes;
    protected $table = "rfp_details";
    protected $fillable = [
        'unique_id', 'rfp_response_type_id',
        'employee_id', 'rfp_site_name', 'rfp_site_address',
        'rfp_site_city', 'rfp_site_postalcode','rfp_published_date',
        'site_visit_available','site_visit_deadline',
        'q_a_deadline_available','qa_deadline',
        'submission_deadline', 'estimated_award_date',
        'announcement_date', 'project_start_date', 'rfp_contact_name',
        'rfp_contact_title_available','rfp_contact_title',
        'rfp_contact_address_available','rfp_contact_address',
        'rfp_phone_number_available','rfp_phone_number',
        'rfp_email_available','rfp_email', 'total_annual_hours',
        'scope_summary', 'force_required', 'term',
        'option_renewal', 'site_unionized', 'union_name',
        'summary_notes', 'rpf_status', 'created_by',
        'updated_by', 'employee_name', 'assign_resource_id'];
        protected $dates = ['deleted_at'];

    public function lastTrack()
    {
        return $this->hasOne('Modules\Contracts\Models\RfpTrackingStage', 'rfp_details_id', 'id')
            ->latest('rfp_process_steps_id');
    }
    public function user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
        //
    }
    public function lastStatusWinLose()
    {
        return $this->hasOne('Modules\Contracts\Models\RfpDetailsWinLose', 'rfp_details_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function evaluationCriteria()
    {
        return $this->hasMany('Modules\Contracts\Models\RfpEvaluationCriteria', 'rfp_details_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function projectExecutionDates()
    {
        return $this->hasMany('Modules\Contracts\Models\RfpProjectExecutionDate', 'rfp_details_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function responseSubmissionDates()
    {
        return $this->hasMany('Modules\Contracts\Models\RfpResponseSubmissionDate', 'rfp_details_id', 'id');
    }

    public function clientOnboarding() {
        return $this->hasOne('Modules\Contracts\Models\ClientOnboarding', 'rfp_details_id', 'id');
    }
}
