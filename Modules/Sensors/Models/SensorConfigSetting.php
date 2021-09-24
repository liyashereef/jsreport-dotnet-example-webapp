<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorConfigSetting extends Model
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
        'sleep_after_trigger',
        'end_trigger_after',
        'created_by',
        'updated_by'
    ];

    /**
     * The customer room
     */
    public function room()
    {
        return
            $this->belongsTo('Modules\Admin\Models\CustomerRoom',
                'room_id',
                'id')
                ->withTrashed();
    }
}
