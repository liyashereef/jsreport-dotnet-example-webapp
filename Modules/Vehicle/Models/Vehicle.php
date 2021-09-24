<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $table   = 'vehicles';

    protected $dates = ['date'];
    protected $fillable = ['make','number','model','year','odometer_reading','purchasing_date','region','active','vin','description'];

    /**
     * User relation
     */
    public function regionDetails()
    {
        return $this->belongsTo('Modules\Admin\Models\RegionLookup', 'region', 'id')->withTrashed();
    }

    public function vehicles()
    {
           return $this->HasMany('Modules\Vehicle\Models\VehicleMaintenanceInterval', 'vehicle_id', 'id');
    }

    public function pendingMaintenance()
    {
           return $this->HasMany('Modules\Vehicle\Models\VehiclePendingMaintenance', 'vehicle_id', 'id');
    }

}
