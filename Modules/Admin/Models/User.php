<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    use HasApiTokens;

    public $timestamps = true;
    protected $connection = 'mysql';
    protected $appends = ['full_name', 'name_with_emp_no'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'alternate_email',
        'username',
        'password',
        'role_id',
        'active',
        'salutation_id',
        'entity',
        'termination_date',
        'gender',
        'marital_status_id',
        'sin',
        'ura_earned',
        'ura_balance',
        'ura_hours'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Relationship: employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function employee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id'); //
    }

    /**
     * Relationship: trashedEmployee
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function trashedEmployee()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id')->withTrashed(); //
    }

    /**
     * Relation to fetch supervisor of a given employee
     * @return type
     */
    public function allocatedSupervisor()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeAllocation', 'user_id', 'id');
    }

    /**
     * Relation to fetch supervisor of a given employee with trashed data
     * @return type
     */
    public function trashedAllocatedSupervisor()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeAllocation', 'user_id', 'id')->withTrashed();
    }

    /**
     * Relation to fetch employee of a given supervisor
     * @return type
     */
    public function allocatedEmployee()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeAllocation', 'supervisor_id', 'id');
    }

    /**
     * Relationship: trashed_employee_profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function trashed_employee_profile()
    {

        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id', 'id')->withTrashed();
        //
    }

    /**
     * Relationship: employee_training
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function employee_training()
    {

        return $this->hasMany('Modules\LearningAndTraining\Models\TrainingUserContent', 'user_id', 'id');
        //
    }

    /**
     * Get full name of user
     *
     * @return void
     */
    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getNameWithEmpNoAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name) . ' (' . $this->trashedEmployee()->first()->employee_no . ')';
    }

    public function getFormatedRoleNameAttribute()
    {
        return str_replace('_', ' ', ucwords($this->roles->first()->name, '_'));
    }

    /**
     * Relationship: employee_profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function employee_profile()
    {
        return $this->hasOne('Modules\Admin\Models\Employee', 'user_id');
    }

    /**
     * Relationship: allocation
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function allocation()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerEmployeeAllocation', 'user_id');
    }

    // public function roles()
    // {
    //     return $this->getRoleNames();
    // }

    // public function userHierarchy()
    // {
    //    return $this->hasMany('App\Models\UserHierarchy', 'user_id', 'id');
    // }

    // public function siteSupervisorCustomer()
    // {
    //    return $this->hasMany('App\Models\SiteSupervisorCustomer', 'user_id', 'id');
    // }

    /**
     * Relation to fetch security clearance of a given user
     * @return type
     */
    public function securityClearanceUser()
    {
        return $this->hasMany('Modules\Admin\Models\SecurityClearanceUser', 'user_id');
    }

    public function employee_shift_payperiods()
    {

        return $this->hasMany('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'employee_id');
    }
    public function candidate_transition()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateEmployee', 'user_id');
    }

    /**
     * Relation to fetch certificates of a given user
     * @return type
     */
    public function userCertificate()
    {
        return $this->hasMany('Modules\Admin\Models\UserCertificate', 'user_id');
    }

    public function eventlog()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EventLogEntry', 'user_id');
    }

    public function eventlog_score()
    {
        return $this->eventlog()->selectRaw('user_id,round(( SUM(score)/(COUNT(*)*2) * 100 ),2) as avg_score,COUNT(score) as prev_attempt')
            ->groupBy('user_id');
    }

    public function eventlog_acceptedshifts()
    {
        return $this->eventlog()->whereRaw("status=1");
    }

    public function expenseAllowedForUser()
    {
        return $this->hasOne('Modules\Expense\Models\ExpenseAllowableForUser', 'user_id');
    }

    public function multipleFillShift()
    {
        return $this->hasOne('Modules\Hranalytics\Models\ScheduleCustomerMultipleFillShifts', 'assigned_employee_id')->orderBy('shift_to', 'DESC')->latest();
    }
    public function emailTemplate()
    {
        return $this->hasMany('Modules\Admin\Models\CustomerTemplateUseridMapping', 'user_id');
    }

    public function idsLocationAllocation()
    {
        return $this->hasMany('Modules\Admin\Models\IdsLocationAllocation', 'user_id');
    }

    public function liveStatus()
    {
        return $this
            ->hasOne('Modules\Timetracker\Models\EmployeeShiftPayperiod', 'employee_id')
            ->with('mostRecentShift:id,employee_shift_payperiod_id,live_status_id')
            ->latest();
    }

    /**
     * Relation to fetch groups of a given employee
     * @return type
     */
    public function allocatedGroups()
    {
        return $this->hasMany('Modules\Admin\Models\KpiGroupEmployeeAllocation', 'user_id', 'id');
    }

    public function dashboardCompliancereports()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeMobileDashboard', 'user_id');
    }

    public function user_salutations()
    {
        return $this->belongsTo('Modules\Admin\Models\UserSalutations', "salutation_id"); //
    }

    public function user_bank()
    {
        return $this->hasOne('Modules\Admin\Models\UserBank', "user_id"); //
    }

    public function user_tax()
    {
        return $this->hasOne('Modules\Admin\Models\UserTax', "user_id"); //
    }



    public function user_emergency_contact()
    {
        return $this->hasOne('Modules\Admin\Models\UserEmergencyContact', "user_id"); //
    }
    public function user_marital_status()
    {
        return $this->belongsTo('Modules\Admin\Models\MaritalStatus', "marital_status_id"); //
    }

    public function user_employments()
    {
        return $this->hasOne('Modules\Admin\Models\UserEmployment', "user_id"); //
    }
    public function user_benefits()
    {
        return $this->hasOne('Modules\Admin\Models\UserBenefit', "user_id"); //
    }
    public function user_skill_value()
    {
        return $this->hasMany('Modules\Admin\Models\UserSkillUserValue', 'user_id');
    }
}
