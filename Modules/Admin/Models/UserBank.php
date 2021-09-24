<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'bank_id', 'transit', 'account_no', 'payment_method_id', 'created_by', 'updated_by'];
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

    public function bank()
    {
        return $this->belongsTo('Modules\Admin\Models\Banks', 'bank_id', 'id'); //
    }
    public function payment_methods()
    {
        return $this->belongsTo('Modules\Admin\Models\UserPaymentMethods', 'payment_method_id', 'id'); //
    }
}
