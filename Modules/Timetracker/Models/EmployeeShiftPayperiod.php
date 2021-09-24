<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EmployeeShiftPayperiod extends Model
{

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = ['employee_schedule_id', 'approved_total_regular_hours', 'is_rated'];

    public function user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
        //
    }

    public function trashed_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id')->withTrashed();
    }

    public function trashed_employee()
    {
        return $this->belongsTo('Modules\Admin\Models\Employee', 'employee_id', 'user_id')->withTrashed();
    }

    /**
     * Relation to fetch supervisor of a given employee with trashed data
     * @return type
     */
    public function trashedAllocatedSupervisor()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeAllocation', 'user_id', 'employee_id')->withTrashed();
    }

    public function approved_by_user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id');
        //
    }

    public function approved_by_trashed_user()
    {

        return $this->belongsTo('Modules\Admin\Models\User', 'approved_by', 'id')->withTrashed();
        //
    }

    public function payperiod()
    {

        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'pay_period_id', 'id');
        //
    }
    public function trashed_payperiod()
    {

        return $this->belongsTo('Modules\Admin\Models\PayPeriod', 'pay_period_id', 'id')->withTrashed();
        //
    }

    public function shifts()
    {

        return $this->hasMany('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id', 'id')->orderBy('start', 'DESC');
        //
    }

    public function submitted_shifts()
    {

        return $this->hasMany('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id', 'id')->whereSubmitted(true)->orderBy('start', 'ASC');
        //
    }

    public function weekly_performance()
    {

        return $this->hasMany('Modules\Timetracker\Models\EmployeeShiftWeeklyPerformance', 'employee_shift_payperiod_id', 'id');
        //
    }

    public function total_hours_by_employee()
    {
        return $this->hasMany('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id', 'id')
            ->selectRaw('employee_shift_payperiod_id,TIME_FORMAT(SEC_TO_TIME( SUM( TIME_TO_SEC( `work_hours` ) ) ), "%H:%i") as total_work_hours')
            ->whereSubmitted(true)
            ->groupBy('employee_shift_payperiod_id');
    }

    public function customer()
    {

        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
        //
    }
    public function trashed_customer()
    {

        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
        //
    }

    public function cpids()
    {
        return $this->hasMany(EmployeeShiftCpid::class, 'employee_shift_payperiod_id', 'id');
    }

    public function earned_billing_amount()
    {
        return $this->cpids()->selectRaw('employee_shift_payperiod_id,SUM(total_amount) as amount')
            ->groupBy('employee_shift_payperiod_id');
    }

    //CR
    public function scopeWithIds($query, $id_arr)
    {
        $query->whereActive(true)->whereIn('id', $id_arr);
    }

    public function fallbackApprovedRegularHours()
    {
        return (!empty($this->approved_total_regular_hours))
        ? $this->approved_total_regular_hours
        : $this->total_regular_hours;
    }

    public function fallbackApprovedOvertimeHours()
    {
        return (!empty($this->approved_total_overtime_hours))
        ? $this->approved_total_overtime_hours
        : $this->total_overtime_hours;
    }

    public function fallbackApprovedStatutoryHours()
    {
        return (!empty($this->approved_total_statutory_hours))
        ? $this->approved_total_statutory_hours
        : $this->total_statutory_hours;
    }

    public function isApproved()
    {
        return $this->approved;
    }

    public function canEdit()
    {
        $user = Auth::user();
        //pass unapproved payperiod
        if (!$this->isApproved()) {
            return true;
        }
        //permitted user can edit the payperiod
        if ($user->can('edit_timesheet')) {
            return true;
        }
        //if the payperiod is approved and the user has no permission to edit (as fallback).
        return false;
    }

    public function latest_shift()
    {
        return $this->hasOne('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id')->whereDate('start', date('Y-m-d'))->latest();

    }
    public function availableShift()
    {
        return $this->hasOne('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id')->wherein('live_status_id', [AVAILABLE, MEETING])->orderBy('start', 'DESC');

    }

    public function mostRecentShift()
    {
        return $this->hasOne('Modules\Timetracker\Models\EmployeeShift', 'employee_shift_payperiod_id')->latest();
    }

    public function employeeSchedule()
    {
        return $this->belongsTo('Modules\Employeescheduling\Models\EmployeeSchedule', 'employee_schedule_id', 'id');
    }

}
