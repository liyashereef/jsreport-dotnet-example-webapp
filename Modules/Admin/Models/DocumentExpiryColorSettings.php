<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentExpiryColorSettings extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = [
        'id', 'grace_period_in_days',
        'grace_period_color_code', 'grace_period_font_color_code', 'alert_period_in_days',
        'alert_period_color_code', 'alert_period_font_color_code', 'overdue_period_color_code', 'overdue_period_font_color_code',
        'active',
        'schedule_grace_period_days',
        'schedule_grace_period_color_code',
        'schedule_grace_period_font_color_code',
        'schedule_alert_period_days',
        'schedule_alert_color_code',
        'schedule_alert_period_font_color_code'
    ];
}
