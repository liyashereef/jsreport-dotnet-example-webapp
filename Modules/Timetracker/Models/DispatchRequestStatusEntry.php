<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchRequestStatusEntry extends Model
{
    public $timestamps = true;
    protected $fillable = ['id','dispatch_request_id','dispatch_request_status_id','respond_by'];
}
