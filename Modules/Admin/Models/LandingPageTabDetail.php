<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPageTabDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'landing_page_tab_id',
        'landing_page_widget_layout_detail_id',
        'landing_page_module_widget_id',
        'landing_page_module_widget_type',
        'created_by',
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function landingPageModuleWidgetName()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageModuleWidget', 'landing_page_module_widget_id', 'id');
    }

    public function landingPageWidgetLayoutDetail()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageWidgetLayoutDetail', 'landing_page_widget_layout_detail_id', 'id');
    }

    public function widgetFields()
    {
        return $this->hasMany('Modules\Admin\Models\LandingPageWidgetField', 'landing_page_tab_detail_id')->withTrashed();
    }

    public function landingPageWidgetTab()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageTab', 'landing_page_tab_id', 'id');
    }
}
