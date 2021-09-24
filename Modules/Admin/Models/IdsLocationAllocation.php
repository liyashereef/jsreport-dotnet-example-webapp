<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdsLocationAllocation extends Model
{
    use SoftDeletes;
    protected $fillable = ['ids_office_id','user_id','created_by','updated_by'];
    
    public function IdsOffice(){
        return $this->belongsTo('Modules\Admin\Models\IdsOffice');
    }
  
    public function User(){
        return $this->belongsTo('Modules\Admin\Models\User');
    }

}
