<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class MobileAppSetting extends Model
{
    protected $fillable = ['time_interval', 'speed_limit', 'trip_show_speed', 'trip_show_distance', 'shift_module_image_limit','key_management_module_image_limit','average_speed_limit','view_ura_balance'];
}
