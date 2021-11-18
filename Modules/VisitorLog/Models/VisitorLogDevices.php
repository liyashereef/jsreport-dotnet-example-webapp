<?php

namespace Modules\VisitorLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogDevices extends Model
{
    use SoftDeletes;
    public $table = 'visitor_log_devices';
    protected $fillable = ['customer_id','uid','activation_code','is_activated','activated_at','activated_by','name','description',
    'device_id','last_active_time','is_blocked','created_by'];


     /**
     * Customer relation
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function visitorLogDeviceSettings()
    {
        return $this->hasOne('Modules\VisitorLog\Models\VisitorLogDeviceSettings', 'visitor_log_device_id');
    }

}
