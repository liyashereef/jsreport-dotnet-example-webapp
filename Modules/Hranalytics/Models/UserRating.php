<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Employee;

class UserRating extends Model
{
    use SoftDeletes;
    //
    public $timestamps = true;

    protected $fillable = ['user_id', 'employee_id','customer_id', 'subject', 'supporting_facts', 'employee_rating_lookup_id', 'payperiod_id', 'rating','policy_id','notify_employee'];

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }
    public function userRating()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingLookup', 'employee_rating_lookup_id', 'id');
    }
    
    public function policyDetails()
    {
        return $this->belongsTo('Modules\Admin\Models\EmployeeRatingPolicies', 'policy_id', 'id')->withTrashed();
    }
    public function employee()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'employee_id', 'id');
    }

    public function customer() {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id','id')->withTrashed();
    }

}
