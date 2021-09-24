<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentRecipient extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['email','priority_id','customer_id','amendment_notification'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function priority()
    {
        return $this->belongsTo('Modules\Admin\Models\IncidentPriorityLookup', 'priority_id')->withTrashed();
    }
}
