<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRatingPolicies extends Model
{
    
    use SoftDeletes;
    protected $fillable = ['id','policy','description'];
    protected $dates = ['deleted_at'];

    public function rating_allocation()
    {
        return $this->hasMany('Modules\Admin\Models\EmployeeRatingPolicyAllocation', 'employee_rating_policy_id', 'id');
    }
}
