<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsOfficeServiceAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['ids_service_id','ids_office_id'];

    public function IdsOffice(){
        return $this->belongsTo('Modules\Admin\Models\IdsOffice')->withTrashed();
    }

}
