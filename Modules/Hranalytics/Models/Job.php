<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{

    use SoftDeletes;
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'unique_key',
        'open_position_id',
        'no_of_vaccancies',
        'job_description',
        //'job_full_description',
        'reason_id',
        'temp_code_id',
        'permanent_id',
        'resign_id',
        'terminate_id',
        'area_manager',
        'am_email',
        'requisition_date',
        'customer_id',
        'requester',
        'email',
        'phone',
        'position',
        'employee_num',
        'assignment_type_id',
        'required_job_start_date',
        'time',
        'ongoing',
        'end',
        'training_id',
        'training_time',
        'training_timing_id',
        'course',
        'notes',
        'shifts',
        'days_required',
        'criterias',
        'vehicle',
        'wage_low',
        'wage_high',
        'remarks',
        'required_attachments',
        'approved_by',
        'hr_rep_id',
        'approved_by',
    ];

    /**
     * Relation towards customer master
     *
     * @return void
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    /**
     * Relation towards Candidate Assignment Types
     *
     * @return void
     */
    public function assignmentType()
    {
        return $this->belongsTo('Modules\Admin\Models\AssignmentTypesLookup', 'assignment_type_id', 'id')->withTrashed();
    }

    /**
     * Relation towards Position lookp
     *
     * @return void
     */
    public function positionBeeingHired()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'open_position_id', 'id')->withTrashed();
    }

    /**
     * Relation towards Position lookp
     *
     * @return void
     */
    public function requestorPosition()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'position', 'id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function assignee()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'hr_rep_id', 'id')->withTrashed();
    }

    public function approver()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id')->withTrashed();
    }

    public function training()
    {
        return $this->belongsTo('Modules\Admin\Models\TrainingTimingLookup', 'training_id', 'id')->withTrashed();
    }

    public function training_timing()
    {
        return $this->belongsTo('Modules\Admin\Models\TimingLookup', 'training_timing_id', 'id')->withTrashed();
    }

    public function experiences()
    {
        return $this->hasMany('Modules\Hranalytics\Models\JobRequiredExperience', 'job_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo('Modules\Admin\Models\JobRequisitionReasonLookup', 'reason_id', 'id')->withTrashed();
    }

    public function reason_temp_code()
    {
        return $this->belongsTo('Modules\Admin\Models\JobRequisitionReasonLookup', 'temp_code_id', 'id')->withTrashed();
    }

    public function reason_permanent()
    {
        return $this->belongsTo('Modules\Admin\Models\JobRequisitionReasonLookup', 'permanent_id', 'id')->withTrashed();
    }

    public function reason_resign()
    {
        return $this->belongsTo('Modules\Admin\Models\JobRequisitionReasonLookup', 'resign_id', 'id')->withTrashed();
    }

    public function reason_terminate()
    {
        return $this->belongsTo('Modules\Admin\Models\JobRequisitionReasonLookup', 'terminate_id', 'id')->withTrashed();
    }

    public function processes()
    {
        return $this->hasMany('Modules\Hranalytics\Models\JobProcess', 'job_id', 'id');
    }

    public function trackingstep()
    {
        return $this->hasOne('Modules\Hranalytics\Models\JobProcess')
            ->orderby('process_id', 'DESC')
            ->latest();
    }

    public function candidate_jobs()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateJob', 'job_id', 'id');
    }

}
