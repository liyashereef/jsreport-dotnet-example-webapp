<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenanceAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['maintenance_id', 'attachment_id'];
    

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }

    public function vehicle_maintenance()
    {
        return $this->belongsTo('Modules\Vehicle\Models\VehicleMaintenanceRecord', 'maintenance_id', 'id');
    }


}
