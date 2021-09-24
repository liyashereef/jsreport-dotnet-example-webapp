<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleMaximumHour extends Model
{

    public $timestamps = true;
    protected $fillable = ['hours'];

}
