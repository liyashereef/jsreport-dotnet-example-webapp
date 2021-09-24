<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorTrigger extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'room_id',
        'sensor_id',
        'trigger_started_at',
        'trigger_ended_at',
        'sleep_after_trigger',
        'end_trigger_after',
        'incident_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The customer where trigger occurred
     */
    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    /**
     * The room where trigger occurred
     */
    public function room()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerRoom', 'room_id', 'id')->withTrashed();
    }

    /**
     * The sensor where trigger occurred
     */
    public function sensor()
    {
        return $this->belongsTo('Modules\Sensors\Models\Sensor', 'sensor_id', 'id')->withTrashed();
    }

    /**
     * The incident corresponding to trigger occurred
     */
    public function incident()
    {
        return $this->belongsTo('Modules\Supervisorpanel\Models\IncidentReport', 'incident_id', 'id')->withTrashed();
    }

    public function sensorLog()
    {
        return $this->hasMany('Modules\Sensors\Models\SensorTriggerLog', 'sensor_trigger_id', 'id');
    }
}
