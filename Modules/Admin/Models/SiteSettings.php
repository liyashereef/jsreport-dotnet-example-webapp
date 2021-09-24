<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = ['shift_duration_limit', 'shift_start_time_tolerance', 'shift_end_time_tolerance'];
}
