<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleEntry extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['customer_id', 'module_id', 'shift_id','shift_start_date', 'field_id', 'field_value', 'attachment_id', 'created_by', 'updated_by'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The customer that belongs to employee allocation
     *
     */
    public function createdUser()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
    public function fieldName()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleField', 'field_id', 'id');
    }

        public function attachments()
    {
        return $this->hasMany('Modules\Admin\Models\ShiftModuleEntryAttachment','shift_module_entry_id','id');
    }

      public function type()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleField', 'field_id', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

}
