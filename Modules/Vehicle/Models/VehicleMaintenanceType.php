<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceType extends Model
{
    use SoftDeletes;
    protected $fillable = ['category_id','name','critical_after_km','critical_after_days','type'];

    public function category()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceCategory', 'category_id', 'id')->withTrashed();
    }

     public function typeDetails()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceDatatype', 'type', 'id');
    }
}
