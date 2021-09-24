<?php

namespace Modules\Timetracker\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class PushNotificationLog extends Eloquent
{


    protected $connection = 'mongodb';
    protected $collection = 'push_notification_response_logs';

    protected $fillable = ['request_id','request_type','title','message','is_read','user_id','status','response','updated_at','created_at'];

    public function dispatch_request(){
      return $this->belongsTo(DispatchRequest::class,'request_id');
    }
}
