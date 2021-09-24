<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleDropdown extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['dropdown_name','info','post_order','detail'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function shiftModuleDropdownOption()
    {
        return $this->hasMany('Modules\Admin\Models\ShiftModuleDropdownOption', 'shift_module_dropdown_id', 'id');
    }

}
