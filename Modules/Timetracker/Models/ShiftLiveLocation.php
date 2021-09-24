<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftLiveLocation extends Model
{
    protected $fillable = ['shift_id','latitude','longitude','accuracy','speed','raw_data','shift_start_time','customer_id','user_id','active'];
}
