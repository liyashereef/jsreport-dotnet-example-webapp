<?php

namespace Modules\Timetracker\Repositories;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\Timetracker\Models\LiveLocation;
class LiveLocationRepository
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
        $this->live_location = new LiveLocation();
    }

    /**
     * Function to send email to supervisor  on timesheet submit
     * @param type $user Guard object
     *
     */
    public function save($inputs)
    {
        return $this->live_location->create($inputs);
    }

    public function getShiftLiveCoordinates($inputs)
    {
        return $this->live_location::select('user_id','dispatch_request_id','latitude','longitude','created_at','is_idle','shift_id','shift_type_id')
        ->orderBy('created_at','DESC')
        ->when(!empty($inputs) && isset($inputs['user_ids']), function ($q) use($inputs) {
            return $q->whereIn('user_id',$inputs['user_ids']);
        })
        ->when(!empty($inputs) && isset($inputs['shift_type_id']), function ($que) use($inputs) {
            $que->whereHas('employee_shift', function ($query) use ($inputs) {
                $query->where('shift_type_id', $inputs['shift_type_id']);
                //$q->whereNull('end');
	        $query->where('live_status_id','!=',3);
            });
        })
	    ->groupBy('user_id')
        ->get()
        ->load([
            'user',
        'user.employee',
        'employee_shift.shift_payperiod.customer.employeeLatestCustomerSupervisor',
        'employee_shift.shift_payperiod.customer.employeeLatestCustomerAreaManager',
        'pending_dispatch_request'
        ]);    
    }

    public function getMSPActiveCoordinates()
    {
       
    }
    
}
