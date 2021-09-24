<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModule extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_id', 'module_name','enable_timeshift','dashboard_view', 'is_active','post_order'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function shiftmodulefield()
    {
        return $this->hasMany('Modules\Admin\Models\ShiftModuleField','module_id','id');
    }

}
