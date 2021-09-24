<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceInterval extends Model
{
    use SoftDeletes;

 
    protected $fillable = ['service_type_id','service_km','vehicle_id','service_date','interval_km','interval_day'];

    /**
     * The user that belongs to employee allocation
     *
     */
    public function vehicle()
    {
        return $this->belongsTo('Modules\Vehicle\Models\Vehicle', 'vehicle_id', 'id');
    }

     /**
     * The user that belongs to employee allocation
     *
     */
    public function serviceType()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceType', 'service_type_id', 'id');
    }
}
