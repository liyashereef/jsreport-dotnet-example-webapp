<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftModuleEntryAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['shift_module_entry_id', 'attachment_id', 'created_by'];
    

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }



    public function shift_module_enrty()
    {
        return $this->belongsTo('Modules\Admin\Models\ShiftModuleEntry', 'shift_module_entry_id', 'id');
    }



}
