<?php

namespace Modules\Admin\Models;
use \Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Database\Eloquent\Model;

class CustomerShifts extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id','shiftname','starttime','endtime'];
    protected $dates = ['deleted_at'];


    public function customer() {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id')->withTrashed();
    }
}
