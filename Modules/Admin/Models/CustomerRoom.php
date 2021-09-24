<?php

namespace Modules\Admin\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRoom extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $columns = array('id','name','severity_id','customer_id');
    protected $fillable = ['customer_id', 'name','severity_id', 'machine_name', 'created_by', 'updated_by'];
    protected $appends = ['room_active_now', 'room_active_configured'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function activeSensors()
    {
        return $this->hasMany('Modules\Sensors\Models\SensorActiveSetting', 'room_id', 'id');
    }

    public function linkedSensors()
    {
        return $this->hasMany('Modules\Sensors\Models\Sensor', 'room_id', 'id')->withTrashed();
    }

    public function linkedIpCameras()
    {
        return $this->hasMany('Modules\IpCamera\Models\IpCamera', 'room_id', 'id')->withTrashed();
    }

    public function getRoomActiveConfiguredAttribute()
    {
        if(isset($this->customer_id) && !empty($this->customer_id)){
            $customerId = $this->customer_id;
        }else{
            $customerId = null;
        }
        $active = false;
        $currentSettingCount = $this->activeSensors()
            ->where("room_id", $this->id)
            ->where("customer_id", $customerId)
            // ->where("customer_id", $this->customer->id)
            ->count();
        if($currentSettingCount > 0) {
            $active = true;
        }
        return $active;
    }

    public function getRoomActiveNowAttribute()
    {
        if(isset($this->customer_id) && !empty($this->customer_id)){
            $customerId = $this->customer_id;
        }else{
            $customerId = null;
        }
        $active = false;
        $now = Carbon::now();
        $currentDay = $now->dayOfWeek;
        $currentSetting = $this->activeSensors()
            ->where("room_id", $this->id)
           ->where("customer_id", $customerId)
            // ->where("customer_id", $this->customer->id)
            ->where("day_id", $currentDay)
            ->where("is_active", true)
            ->latest()->first();
        if (isset($currentSetting->start_time) && isset($currentSetting->end_time)) {
            $start = Carbon::createFromTimeString($currentSetting->start_time);
            $end = Carbon::createFromTimeString($currentSetting->end_time);
            if ($now->gte($end)) {
                $end = $end->addDay();
            }
            else if ($end->lte($start)) {
                $start = $start->subDay();
            }
            if ($now->between($start, $end, true)) {
                $active = true;
            }
            return $active;
        } else {
            return $active;
        }

    }
}
