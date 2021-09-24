<?php

namespace Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleDamageAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['trip_id','vehicle_damage_time', 'attachment_id'];
    

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }



  //  public function shift_module_enrty()
  //  {
  //      return $this->belongsTo('Modules\Admin\Models\ShiftModuleEntry', 'shift_module_entry_id', 'id');
  //  }



}
