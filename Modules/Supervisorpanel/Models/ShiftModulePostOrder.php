<?php

namespace Modules\Supervisorpanel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModulePostOrder extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_id', 'module_id', 'shift_id', 'shift_start_date', 'field_id','dropdown_id','field_value', 'duration', 'percentage', 'created_by', 'updated_by'];

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id');
    }

    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }

}
