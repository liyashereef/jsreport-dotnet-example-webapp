<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeExitInterview extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'unique_id',
        'project_id',
        'user_id',
        'exit_interview_reason_id',
        'exit_interview_reason_details',
        'exit_interview_explanation',
        'created_by',
    ];

    public function regional_manager()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function customer()
    {

        return $this->belongsTo('Modules\Admin\Models\customer', 'project_id', 'id')->withTrashed();
    }
    public function active_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function reason_detail_termination()
    {
        return $this->belongsTo('Modules\Admin\Models\ExitTerminationReasonLookup', 'exit_interview_reason_details', 'id')->withTrashed();
    }
    public function reason_detail_resignation()
    {
        return $this->belongsTo('Modules\Admin\Models\ExitResignationReasonLookup', 'exit_interview_reason_details', 'id')->withTrashed();
    }
}
