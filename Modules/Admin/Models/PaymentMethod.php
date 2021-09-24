<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use SoftDeletes;

    public $table = 'payment_methods';
    protected $fillable = ['paymentmethod','status','createdby'];
    protected $dates = ['deleted_at'];
}
