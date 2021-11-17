<?php

namespace Modules\VisitorLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogDeviceSettings extends Model
{
    use SoftDeletes;
    public $table = 'visitor_log_device_settings';
    protected $fillable = ['visitor_log_device_id','device_id','template_id','pin','camera_mode','scaner_camera_mode'];


    public function visitorLogTemplates()
    {
        return $this->belongsTo('Modules\Admin\Models\VisitorLogTemplates', 'template_id', 'id')->withTrashed();
    }
}
