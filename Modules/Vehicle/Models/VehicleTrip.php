<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleTrip extends Model
{
    use SoftDeletes;

 
    protected $fillable = ['shift_id','vehicle_id','customer_id','user_odometer_start','user_odometer_end','user_distance_travelled','system_odometer_start','system_odometer_end','system_distance_travelled','start_visible_damage','end_visible_damage','start_notes','end_notes','start_datetime','end_datetime','created_by','updated_by'];
    public function vehicle()
    {
        return $this->belongsTo('Modules\Vehicle\Models\Vehicle', 'vehicle_id', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift', 'shift_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany('Modules\Vehicle\Models\VehicleDamageAttachment','trip_id','id');
    }
}
