<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleField extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['module_id', 'field_name', 'field_type', 'dropdown_id', 'is_multiple_photo', 'order_id', 'is_active', 'status', 'field_status'];

    public function module()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModule', 'module_id', 'id');
    }

    public function fieldtype()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleFieldType', 'field_type', 'id');
    }

    public function dropdown()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleDropdown', 'dropdown_id', 'id');
    }

}
