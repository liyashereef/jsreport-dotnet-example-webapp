<?php

namespace Modules\Timetracker\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Modules\Admin\Models\User;
use Modules\Sensors\Models\SensorActiveSetting;

/**
 * @property mixed user_id
 * @property mixed user
 * @property mixed sensor_id
 * @property mixed sensor
 * @property mixed room
 * @property mixed customer
 * @property mixed topic
 * @property mixed data
 * @property mixed valid
 */
class SensorCommunicationLog extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'sensor_communication_log';
    protected $fillable = [
        'user_id',
        'user',
        'sensor_id',
        'sensor',
        'room',
        'customer',
        'url',
        'topic',
        'data',
        'valid',
        'timestamp'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function sensor()
    {
        return $this->belongsTo(SensorActiveSetting::class,'sensor_id');
    }

}
