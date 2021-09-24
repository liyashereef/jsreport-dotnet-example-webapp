<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorTriggerLog extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id',
        'sensor_trigger_id',
        'trigger_started_at',
        'trigger_ended_at',
        'created_by',
        'updated_by'
    ];

    /**
     * The sensor where trigger occurred
     */
    public function sensor()
    {
        return $this->belongsTo('Modules\Sensors\Models\Sensor', 'sensor_id', 'id');
    }

    /**
     * The incident corresponding to trigger occurred
     */
    public function sensorTrigger()
    {
        return $this->belongsTo('Modules\Sensors\Models\SensorTrigger', 'sensor_trigger_id', 'id');
    }
}
