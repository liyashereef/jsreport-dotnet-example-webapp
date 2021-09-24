<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StcThresholdSetting extends Model
{
    use SoftDeletes;
    protected $fillable = ['no_of_days_critical', 'critical_days_color', 'critical_days_font_color', 'no_of_days_major', 'major_days_color', 'major_days_font_color', 'no_of_days_minor', 'minor_days_color', 'minor_days_font_color', 'stc_threshold_hours'];
}
