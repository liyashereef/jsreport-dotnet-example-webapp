<?php

namespace Modules\IpCamera\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpCameraConfigurationTabDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'ip_camera_configuration_tab_id',
        'landing_page_widget_layout_detail_id',
        'ip_camera_id',
        'landing_page_module_widget_type',
        'created_by',
    ];
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function moduleWidgetName()
    {
        return $this->belongsTo('Modules\IpCamera\Models\IpCamera', 'ip_camera_id', 'id');
    }

    public function widgetLayoutDetail()
    {
        return $this->belongsTo('Modules\Admin\Models\LandingPageWidgetLayoutDetail', 'landing_page_widget_layout_detail_id', 'id');
    }

    public function widgetFields()
    {
        return $this->hasMany('Modules\IpCamera\Models\IpCameraConfigurationWidgetField', 'ip_camera_configuration_tab_detail_id')->withTrashed();
    }

    public function widgetTab()
    {
        return $this->belongsTo('Modules\IpCamera\Models\IpCameraConfigurationTab', 'ip_camera_configuration_tab_id', 'id');
    }

}
