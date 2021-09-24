<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Admin\Models\Customer;
use Modules\Admin\Models\User;

class UniformOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'site_id',
        'price',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'updated_by',
        'ura_deducted'
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function site()
    {
        return $this->belongsTo(Customer::class, 'site_id');
    }

    public function statusLog()
    {
        return $this->hasMany(UniformOrderStatusLog::class, 'uniform_order_id')->orderBy('id', 'desc')->withTrashed();
    }

    public function orderItems()
    {
        return $this->hasMany(UniformOrderItem::class, 'uniform_order_id');
    }
}
