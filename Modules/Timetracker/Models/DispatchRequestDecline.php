<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\User;

class DispatchRequestDecline extends Model
{
    use SoftDeletes;
    protected $fillable = ['dispatch_request_id','user_id','comment'];

    public function dispatch_request(){
        return $this->belongsTo(DispatchRequest::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
