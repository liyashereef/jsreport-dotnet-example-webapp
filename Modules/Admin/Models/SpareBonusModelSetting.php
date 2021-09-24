<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpareBonusModelSetting extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'reliability_grace_period_in_days',
        'reliability_grace_period_color_code',
        'reliability_grace_period_font_color_code',
        'reliability_alert_period_in_days',
        'reliability_alert_period_color_code',
        'reliability_alert_period_font_color_code',
        'reliability_safe_score',
        'reliability_safe_score_color_code',
        'reliability_safe_score_font_color_code',
        'reliability_rank_top_level',
        'reliability_rank_top_level_color_code',
        'reliability_rank_top_level_font_color_code',
        'reliability_rank_average_level',
        'reliability_rank_average_level_color_code',
        'reliability_rank_average_level_font_color_code',
        'schedule_top_rank_message',
        'schedule_average_rank_message',
        'schedule_below_average_rank_message',
    ];
}
