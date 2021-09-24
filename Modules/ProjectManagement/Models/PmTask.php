<?php

namespace Modules\ProjectManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PmTask extends Model
{

    use SoftDeletes;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['name', 'due_date','completed_date', 'followers_can_update', 'project_id', 'group_id', 'site_id', 'is_completed', 'created_by', 'updated_by', 'rated_by', 'deadline_rating_id', 'value_add_rating_id', 'initiative_rating_id', 'commitment_rating_id', 'complexity_rating_id', 'efficiency_rating_id', 'rating_notes', 'rated_at', 'unique_key', 'average_rating','deadline_weightage','value_add_weightage','initiative_weightage','commitment_weightage','complexity_weightage','efficiency_weightage'];


    /**
     * The customer details that belongs to project management
     *
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'site_id', 'id');
    }

    /**
     * The project details that belongs to project management
     *
     */
    public function projects()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmProject', 'project_id', 'id');
    }

    /**
     * The project details that belongs to project management with trashed
     *
     */
    public function projectsWithTrashed()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmProject', 'project_id', 'id')->withTrashed();
    }

    /**
     * The group details that belongs to project management with trashed
     *
     */
    public function groupDetailsWithTrashed()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmGroup', 'group_id', 'id')->withTrashed();
    }

    /**
     * The group details that belongs to project management
     *
     */
    public function groupDetails()
    {
        return $this->belongsTo('Modules\ProjectManagement\Models\PmGroup', 'group_id', 'id');
    }

    /**
     * The status details that belongs to project management
     *
     */
    public function status()
    {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTaskStatus', 'task_id', 'id')
            ->orderBy('status_date', 'DESC');
    }

    /**
     * The assigned user details that belongs to project management
     *
     */
    public function taskOwners()
    {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTaskOwner', 'task_id', 'id')->where('type', 0);
    }

    /**
     * The assigned user details that belongs to project management
     *
     */
    public function followers()
    {
        return $this->hasMany('Modules\ProjectManagement\Models\PmTaskOwner', 'task_id', 'id')->where('type', 1);
    }

    /**
     * The deadline rating details that belongs to project management
     *
     */
    public function deadlineRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'deadline_rating_id', 'id');
    }

    /**
     * The value add rating details that belongs to project management
     *
     */
    public function valueAddRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'value_add_rating_id', 'id');
    }

    /**
     * The initiative rating details that belongs to project management
     *
     */
    public function initiativeRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'initiative_rating_id', 'id');
    }

    /**
     * The commitment rating details that belongs to project management
     *
     */
    public function commitmentRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'commitment_rating_id', 'id');
    }

    /**
     * The complexity rating details that belongs to project management
     *
     */
    public function complexityRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'complexity_rating_id', 'id');
    }

    /**
     * The efficiency rating details that belongs to project management
     *
     */
    public function efficiencyRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'efficiency_rating_id', 'id');
    }

    /**
     * The created user details that belongs to project management with trashed
     *
     */
    public function createdBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    /**
     * The rating details that belongs to project management
     *
     */
    public function rating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'rating_id', 'id');
    }

    /**
     * The  with and whereHas query combined
     *
     */
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->with([$relation => $constraint])->whereHas($relation, $constraint);
    }
}
