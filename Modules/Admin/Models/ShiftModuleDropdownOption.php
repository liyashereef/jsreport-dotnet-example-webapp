<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleDropdownOption extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['option_name','option_info', 'shift_module_dropdown_id', 'order_sequence'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Relation to template settings
     *
     * @return type
     */
    public function shiftModuleDropdown()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleDropdown', 'shift_module_dropdown_id', 'id');
    }
}
