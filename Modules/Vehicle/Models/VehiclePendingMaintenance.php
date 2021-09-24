<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehiclePendingMaintenance extends Model
{
    use SoftDeletes;
    protected $table   = 'vehicle_pending_maintenance';

    protected $fillable = ['vehicle_id','type_id','service_date','service_kilometre','service_due','service_critical'];
    public function vehicle()
    {
        return $this->belongsTo('Modules\Vehicle\Models\Vehicle', 'vehicle_id', 'id')->withTrashed();
    }

     public function maintenanceType()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceType', 'type_id', 'id');
    }
}
