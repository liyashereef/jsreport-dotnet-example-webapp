<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\User;

class UniformOrderStatusLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uniform_order_id',
        'uniform_order_status_id',
        'notes',
        'is_email_required',
        'created_by',
    ];

    public function orderStatus()
    {
        return $this->belongsTo(UniformOrderStatus::class, 'uniform_order_status_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
