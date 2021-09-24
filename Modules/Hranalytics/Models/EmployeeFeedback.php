<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFeedback extends Model
{
    use SoftDeletes;
    protected $table = "employee_feedback";
    protected $fillable = [
        "customer_id",
        "subject",
        "message",
        "department_id",
        "rating_id",
        "status",
        "latitude",
        "longitude",
        "created_by",
        "updated_by"
    ];

    /**
     * The user that belongs to employee allocation
     *
     */
    public function create_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function update_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id')->withTrashed();
    }


    public function department()
    {
        return $this->belongsTo('Modules\Admin\Models\DepartmentMaster', 'department_id')->withTrashed();
    }


    public function userstatus()
    {
        return $this->belongsTo('Modules\Admin\Models\WhistleblowerStatusLookup', 'status', "id");
    }

    public function employeeRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'rating_id')->withTrashed();
    }

    /**
     * Get all of the comments for the EmployeeFeedback
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvalfeedback()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeFeedbackApproval', 'feedback_id');
    }
}
