<?php

namespace Modules\Timetracker\Repositories;

use DB;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Models\User;
use Modules\Timetracker\Models\Notification;
use Modules\Timetracker\Models\StatusNotification;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;

class NotificationRepository
{

    /**
     * The Model instance.
     * ''
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model,$userrepository,$customeremployeeallocationrepository;

    /**
     * Create a new NotificationRepository instance.
     *
     * @param  \App\Models\Notification $Notification
     */
    public function __construct(Notification $notification,UserRepository $userrepository,CustomerEmployeeAllocationRepository $customeremployeeallocationrepository)
    {
        $this->model = $notification;
        $this->userrepository = $userrepository;
        $this->customeremployeeallocationrepository = $customeremployeeallocationrepository;
    }

    /**
     * Get Notificationlist
     *
     * @param  \App\Models\Notification  $Notification
     * @return resultset
     */
    public function getNotifications($read = null)
    {
        $all_notification = StatusNotification::whereActive(true)
            ->with(array(
                'notification' => function ($query) {
                    $query->select('id', 'employee_id', 'notification_message', 'active',
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i") as created'),
                        DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d %H:%i") as updated'));
                },
                'notification.user_notification_guard.employee_profile'))
            ->where('user_id', '=', Auth::user()->id)
            ->where(function ($query) use ($read) {
                if (!is_null($read)) {
                    $query->whereRead($read);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
        return $all_notification;
    }

    public function formatPayperiod($date_input)
    {
        return date('d M Y', strtotime($date_input));
    }

    public function createNotification($employeeShiftPayperiod, $type)
    {
        $supervisor_full_name_user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $guard_full_name_user = $employeeShiftPayperiod->trashed_user->first_name . ' ' . $employeeShiftPayperiod->trashed_user->last_name;
        switch ($type) {
            case 'TIME_SHEET_SUBMITTED':
                $notification_message = 'Weekly Timesheet <b>(' . $this->formatPayperiod($employeeShiftPayperiod->trashed_payperiod->start_date) . ' - ' . $this->formatPayperiod($employeeShiftPayperiod->trashed_payperiod->end_date) . ')</b> of <b>' . $guard_full_name_user . '</b> submitted';
                break;
            case 'TIME_SHEET_APPROVED':
                $notification_message = 'Weekly Timesheet <b>(' . $this->formatPayperiod($employeeShiftPayperiod->trashed_payperiod->start_date) . ' - ' . $this->formatPayperiod($employeeShiftPayperiod->trashed_payperiod->end_date) . ')</b> of <b>' . $guard_full_name_user . '</b> approved by <b>' . $supervisor_full_name_user . '</b>';
                break;
        }
        $employee_id = $employeeShiftPayperiod->employee_id;
        $data_notification = ['employee_id' => $employeeShiftPayperiod->employee_id, 'notification_message' => $notification_message];
        $notification_id = Notification::Create($data_notification)->id;
        $supervisors = \Modules\Admin\Models\EmployeeAllocation::
                            where('user_id', '=', $employeeShiftPayperiod->employee_id)->get();

        $projectid = $employeeShiftPayperiod->customer_id;
        
        $data_notification_status = [];
        foreach ($supervisors as $supervisor) {
            $supervisorid = $supervisor->supervisor_id;
            $arr_user = [$supervisorid];
            $customerarray = $this->customeremployeeallocationrepository->getAllocatedCustomerId($arr_user,false);
            if(in_array($projectid,$customerarray))
            {
                $data_notification_status[] = ['notification_id' => $notification_id, 'user_id' => $supervisor->supervisor_id];
            }
            
        }
        $admin = $this->userrepository->getUserList(true, ['admin','super_admin']);       
        /*
        $admin = User::whereActive(true)->with(['roles' => function ($query) {
            return $query->where('name', '=', 'admin')->orWhere('name', '=', 'super_admin');
        }])->get(); 
        */

        foreach ($admin as $admin_data) {
            $data_notification_status[] = ['notification_id' => $notification_id, 'user_id' => $admin_data->id];
        }
        StatusNotification::insert($data_notification_status);
        return true;
    }

    public function readNotifications($notification_id_list)
    {
        foreach ($notification_id_list as $notification_id) {
            $read_notification[] = StatusNotification::where('notification_id', '=', $notification_id)
                ->where('user_id', '=', Auth::user()->id)
                ->update(['read' => 1]);
        }
    }

    public function deleteNotifications($request)
    {
        $delete_notification = StatusNotification::where('notification_id', '=', $request->get('id'))
            ->where('user_id', '=', Auth::user()->id)
            ->update(['active' => 0]);
        return $delete_notification;
    }

    public function multiDeleteNotifications($notification_id_list)
    {
        foreach ($notification_id_list as $notification_id) {
            StatusNotification::where('notification_id', '=', $notification_id)
                ->where('user_id', '=', Auth::user()->id)
                ->update(['active' => 0]);
        }
    }

}
