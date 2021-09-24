<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sensor extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 'nod_mac', 'pan_mac', 'gateway_mac', 'machine_name', 'online', 'online_updated_at', 'latest_detection_at', 'low_battery', 'low_battery_updated', 'room_id', 'room_allocated_at', 'enabled', 'created_by', 'updated_by'
    ];

    /**
     * The customer that belongs to employee allocation
     *
     */
    public function room()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerRoom', 'room_id', 'id')->withTrashed();
    }
}
