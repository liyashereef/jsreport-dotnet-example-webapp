<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleSettings extends Model
{
    protected $fillable = ['weekly_threshold','bi_weekly_threshold'];
}
