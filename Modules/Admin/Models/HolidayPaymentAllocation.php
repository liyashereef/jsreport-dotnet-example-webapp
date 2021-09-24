<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\Holiday;

class HolidayPaymentAllocation extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['paymentstatus','status'];

    protected $dates = ['deleted_at'];
}
