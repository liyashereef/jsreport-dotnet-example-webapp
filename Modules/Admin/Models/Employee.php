<?php

namespace Modules\Admin\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'employee_no',
        'phone',
        'phone_ext',
        'cell_no',
        'work_type_id',
        'image',
        'employee_no',
        'employee_city',
        'employee_address',
        'employee_full_address',
        'employee_postal_code',
        'geo_location_lat',
        'geo_location_long',
        'employee_work_email',
        'employee_vet_status',
        'vet_release_date',
        'vet_enrollment_date',
        'vet_service_number',
        'employee_doj',
        'employee_dob',
        'employee_rating',
        'current_project_wage',
        'position_id',
        'years_of_security',
        'being_canada_since',
        'wage_expectations_from',
        'wage_expectations_to',
        'time_sheet_approval_rating'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function work_type()
    {

        return $this->belongsTo('Modules\Admin\Models\WorkType', 'work_type_id', 'id');
        //
    }

    public function user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id');
        //
    }

    public function trashedUser()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
        //
    }
    public function getAgeAttribute()
    {
        return Carbon::parse($this->attributes['employee_dob'])->age;
    }
    public function getServiceLengthAttribute()
    {
        return Carbon::parse($this->attributes['employee_doj'])->age;
    }
    public function employeePosition()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'position_id', 'id');
    }

    public function trashed_employee_position()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'position_id', 'id')->withTrashed();
    }

    public function employee_availability()
    {
        return $this->hasMany('Modules\Timetracker\Models\EmployeeAvailability', 'employee_id', 'user_id');
    }
    public function employee_unavailability()
    {
        return $this->hasMany('Modules\Timetracker\Models\EmployeeUnavailability', 'employee_id', 'user_id');
    }
}
