<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeOffSplitUp extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'timeoff_request_type_setting_id',
        'available_days',
        'booked_days',
        'year',
        'created_by',
        'updated_by'
    ];
}
