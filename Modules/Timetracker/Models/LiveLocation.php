<?php

namespace Modules\Timetracker\Models;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Modules\Admin\Models\User;

class LiveLocation extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'emp_locations';
    protected $fillable = ['user_id', 'shift_id', 'shift_type_id', 'latitude', 'longitude','dispatch_request_id'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function dispatchRequest()
    {
        return $this->belongsTo(DispatchRequest::class);
    }
    public function pending_dispatch_request()
    {
        return $this->belongsTo(DispatchRequest::class, 'dispatch_request_id', 'id')
            ->whereIn('dispatch_request_status_id', DISPATCH_PROGRESS_STATUS);
    }
    public function employee_shift()
    {
        return $this->belongsTo(EmployeeShift::class,'shift_id');
    }

}
