<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRatingPolicyAllocation extends Model
{
    protected $fillable = ["id","employee_rating_policy_id","employee_rating_id"];

    function policy() {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingPolicies', 'employee_rating_policy_id', 'id');
    }
    function rating() {
        return $this->belongsTo('App\Modules\Admin\Models\EmployeeRatingLookup');
    }
}
