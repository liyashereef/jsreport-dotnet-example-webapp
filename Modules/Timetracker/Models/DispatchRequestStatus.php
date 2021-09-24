<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchRequestStatus extends Model
{
    use SoftDeletes;
    protected $table = 'dispatch_request_statuses';
    protected $fillable = ['id','name'];
}
