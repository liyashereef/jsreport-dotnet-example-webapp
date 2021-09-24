<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceRecord extends Model
{
    use SoftDeletes;


    protected $fillable = ['vehicle_id','type_id','vendor_id','service_date','service_kilometre','odometer_reading','interval','notes','total_charges','tax','tax_amount','subtotal','created_by','updated_by'];
    public function vehicle()
    {
        return $this->belongsTo('Modules\Vehicle\Models\Vehicle', 'vehicle_id', 'id')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleVendorLookup', 'vendor_id', 'id')->withTrashed();
    }


     public function maintenanceType()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceType', 'type_id', 'id')->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany('Modules\Vehicle\Models\VehicleMaintenanceAttachment','maintenance_id','id');
    }
}
