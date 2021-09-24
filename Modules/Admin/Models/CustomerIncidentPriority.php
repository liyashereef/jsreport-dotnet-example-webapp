<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerIncidentPriority extends Model
{
    use SoftDeletes;
    public $table = 'customer_incident_priority';
    public $timestamps = true;
    protected $fillable = ['priority_id','customer_id','response_time'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function priority()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentPriorityLookup', 'priority_id', 'id');
    }
}
