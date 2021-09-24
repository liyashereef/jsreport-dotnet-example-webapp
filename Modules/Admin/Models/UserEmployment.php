<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserEmployment extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'continuous_seniority', 'pay_detach_customer_id', 'created_by', 'updated_by'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Relationship: user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id'); //
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'pay_detach_customer_id');
    }
}
