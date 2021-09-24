<?php

namespace Modules\Uniform\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformOrderStatus extends Model
{
    use SoftDeletes;
    protected $table = 'uniform_order_status';

    protected $fillable = [
        'display_name',
        'machine_code',
        'immutable'
    ];
}
