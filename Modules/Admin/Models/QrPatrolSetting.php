<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrPatrolSetting extends Model
{
    use SoftDeletes;
    protected $fillable = ['days_prior', 'critical_level_percentage', 'acceptable_level_percentage'];
}
