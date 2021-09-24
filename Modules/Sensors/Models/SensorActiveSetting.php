<?php

namespace Modules\Sensors\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SensorActiveSetting extends Model
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
        'customer_id',
        'room_id',
        'day_id',
        'is_active',
        'start_time',
        'end_time',
        'created_by',
        'updated_by',
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

    /**
     * @return mixed
     */
    public function customer()
    {
        return
            $this->belongsTo('Modules\Admin\Models\Customer',
                'customer_id',
                'id')
                ->withTrashed();
    }

    /**
     * @return mixed
     */
    public function dayname()
    {
        return $this->belongsTo('Modules\Admin\Models\Day', 'day_id')->withTrashed();
    }
}
