<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitingAnalyticsWidgetField extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'recruiting_analytics_tab_detail_id',
        'field_display_name',
        'field_system_name',
        'default_sort',
        'default_sort_order',
        'visible',
        'created_by',
        'permission_text',
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function widgetTabDetail()
    {
        return $this->belongsTo('Modules\Admin\Models\RecruitingAnalyticsTabDetail', 'recruiting_analytics_tab_detail_id', 'id');
    }
}
