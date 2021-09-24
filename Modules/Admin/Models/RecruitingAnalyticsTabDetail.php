<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitingAnalyticsTabDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'recruiting_analytics_tab_id',
        'landing_page_widget_layout_detail_id',
        'landing_page_module_widget_id',
        'landing_page_module_widget_type',
        'created_by',
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function moduleWidgetName()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageModuleWidget', 'landing_page_module_widget_id', 'id');
    }

    public function widgetLayoutDetail()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageWidgetLayoutDetail', 'landing_page_widget_layout_detail_id', 'id');
    }

    public function widgetFields()
    {
        return $this->hasMany('Modules\Admin\Models\RecruitingAnalyticsWidgetField', 'recruiting_analytics_tab_detail_id')->withTrashed();
    }

    public function widgetTab()
    {
        return $this->belongsTo('Modules\Admin\Models\RecruitingAnalyticsTab', 'recruiting_analytics_tab_id', 'id');
    }
}
