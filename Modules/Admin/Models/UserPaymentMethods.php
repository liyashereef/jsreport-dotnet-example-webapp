<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPaymentMethods extends Model
{
    use SoftDeletes;
    protected $fillable = ['payment_methods', 'apogee_code', 'created_by', 'updated_by'];
}
