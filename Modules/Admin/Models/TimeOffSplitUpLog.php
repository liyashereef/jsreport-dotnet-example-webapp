<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class TimeOffSplitUpLog extends Moloquent
{
    protected $connection = "mongodb";
    protected $collection = 'time_off_split_up_log';
    protected $created_at = 'Y-m-d H:i:s';
    protected $fillable = [
        'id',
        'user_id',
        'request_type',
        'timeoff_request_type_setting_id',
        'available_days',
        'booked_days',
        'year',
        'accrual',
        'doj',
        'created_by',
        'created_at',
        'updated_at'
    ];
}
