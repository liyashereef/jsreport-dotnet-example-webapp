<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardSetting extends Model
{
    use SoftDeletes;
    protected $fillable = ["id", "default_employeesurvey"];
}
