<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchRequestPushNotificationCustomers extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['dispatch_request_id','customer_id'];
}
