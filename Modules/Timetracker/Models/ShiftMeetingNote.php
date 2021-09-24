<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftMeetingNote extends Model
{
    public $timestamps = true;
    protected $fillable = ['shift_id','note','time'];
}
