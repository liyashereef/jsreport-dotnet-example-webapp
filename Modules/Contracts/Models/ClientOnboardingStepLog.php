<?php

namespace Modules\Contracts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientOnboardingStepLog extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'step_id',
        'step',
        'onboarding_id',
        'section_id',
        'section',
        'target_date',
        'assigned_to',
        'step_percentage_completed',
        'assignee',
        'created_by',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function sectionTemplate()
    {
        return $this->belongsTo('Modules\Admin\Models\ClientOnboardingTemplateSection', 'section_id', 'id');
    }

    public function section()
    {
        return $this->belongsTo('Modules\Contracts\Models\ClientOnboardingSection', 'section_id', 'id');
    }

    public function step()
    {
        return $this->belongsTo('Modules\Contracts\Models\ClientOnboardingStep', 'step_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User','created_by','id')->withTrashed();
    }

    public function assignedTo()
    {
        return $this->belongsTo('Modules\Admin\Models\User','assigned_to','id')
            ->select('id','first_name','last_name')
            ->with('employee:id,user_id,employee_no')
            ->withTrashed();
    }


}
