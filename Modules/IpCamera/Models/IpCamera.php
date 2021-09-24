<?php

namespace Modules\IpCamera\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IpCamera extends Model
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
        'name',
        'machine_name',
        'unique_id',
        'online',
        'credential_username',
        'credential_password',
        'ip',
        'rtsp_port',
        'controller_port',
        'online_updated_at',
        'low_battery',
        'low_battery_updated_at',
        'room_id',
        'room_allocated_at',
        'enabled',
        'created_by',
        'updated_by'
    ];

    /**
     * The customer room
     */
    public function room()
    {
        return
            $this->belongsTo(
                'Modules\Admin\Models\CustomerRoom',
                'room_id',
                'id'
            )
            ->withTrashed();
    }
}
