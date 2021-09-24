<?php

namespace Modules\Timetracker\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */

    /**
     * Create a new EmailRepository instance.
     *
     * @param  \App\Models\Notification $Notification
     */
    public function __construct()
    {

    }

    /**
     * Function to send email to supervisor  on timesheet submit
     * @param type $user Guard object
     *
     */
    public function emailSupervisor($user)
    {
        $alloc_details = \Modules\Admin\Models\EmployeeAllocation::where('user_id', $user->id)->with('supervisor')->get();
        foreach ($alloc_details as $allocation) {
            if (isset($allocation->supervisor)) {
                Mail::to($allocation->supervisor->email)->queue(new \Modules\Timetracker\Mail\SubmitTimesheet($user->full_name, $allocation->supervisor->full_name));
            }
        }
    }

    /**
     * Function to send email to admin on timesheet approval
     * @param type $user Guard object
     *
     */
    public function emailAdmin($employeeShiftPayperiod)
    {
        $supervisor_full_name_user = Auth::user()->full_name;
        $guard_full_name_user = $employeeShiftPayperiod->trashed_user->full_name;

        $admin_obj = \Modules\Admin\Models\User::where('id', 1)->get(['first_name', 'email']);
        foreach ($admin_obj as $admin) {
            Mail::to($admin->email)->queue(new \Modules\Timetracker\Mail\ApproveTimesheet($admin->name, $guard_full_name_user, $supervisor_full_name_user));
        }
    }

    /**
     * Function to send email to supervisor  on timesheet submit
     * @param type $user Guard object
     *
     */
    public function emailAreaManager($employee_shift, $guard_tour_counts = null)
    {
        $customer_id = $employee_shift->shift_payperiod->trashed_customer->id;
        $alloc_details = \Modules\Admin\Models\CustomerEmployeeAllocation::where('customer_id', $customer_id)
            ->with('areaManager')
            ->get();
        foreach ($alloc_details as $allocation) {
            if (isset($allocation->areaManager)) {
                try {
                    info('Notification will sent to ' . $allocation->areaManager->email);
                    Mail::to($allocation->areaManager->email)->queue(new \Modules\Timetracker\Mail\GuardTour($allocation->areaManager, $employee_shift, $guard_tour_counts));
                } catch (\Exception $e) {
                    info($e->getMessage());
                }
            }
        }
    }
}
