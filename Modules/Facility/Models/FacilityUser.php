<?php

namespace Modules\Facility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FacilityUser extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'alternate_email','phoneno', 'username', 'password','unit_no','customer_id','internaluser', 'active',
    ];

    public function FacilityServiceUserAllocation(){
        return $this->hasMany('Modules\Facility\Models\FacilityServiceUserAllocation', 'facility_user_id','id');
    }

    public function ServiceFacilityUserAllocation(){
        return $this->hasMany('Modules\Facility\Models\FacilityServiceUserAllocation', 'facility_user_id','id');
    }

    public function customer(){
        return $this->hasOne('Modules\Admin\Models\Customer', 'id','customer_id');
    }


}
