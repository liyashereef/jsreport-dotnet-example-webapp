<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchRequest extends Model
{
    use SoftDeletes;
    
    public $timestamps = true;
    protected $connection = 'mysql';
    protected $fillable = ['subject','dispatch_request_type_id','customer_id','site_address','site_postalcode','is_existing_customer','name','latitude','longitude','rate','created_by','description','dispatch_request_status_id','respond_by','respond_at','estimated_time','actual_time','delta'];


    public function dispatchRequestType()
    {
        return $this->belongsTo(DispatchRequestType::class)
            ->select('id','name','rate','description')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id');
    }
    public function customer_trashed()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id')->withTrashed();
    }
    public function customer_data()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id')
            ->select('id','project_number','client_name','address','city','postal_code','geo_location_lat','geo_location_long');
    }

    public function respondby()
    {
        return $this->belongsTo('Modules\Admin\Models\User','respond_by')
            ->select('id','first_name','last_name','username','email','alternate_email');
    }
    public function createdby()
    {
        return $this->belongsTo('Modules\Admin\Models\User','createdby')
            ->select('id','first_name','last_name','username','email','alternate_email');
    }

    public function dispatchRequestStatus()
    {
        return $this->belongsTo(DispatchRequestStatus::class)->select('id','name');
    }

    public function PushNotificationCustomers(){
        return $this->hasMany(DispatchRequestPushNotificationCustomers::class);
    }
    public function PushNotificationLog(){
        return $this->hasMany(PushNotificationLog::class);
    }


}

