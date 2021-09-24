<?php

namespace Modules\IpCamera\Models;

use Illuminate\Database\Eloquent\Model;

class IpCameraRoomAllocationHistories extends Model
{
    protected $dates = ['deleted_at'];
    protected $fillable = ['ipcamera_id', 'room_id', 'is_linked'];
}
