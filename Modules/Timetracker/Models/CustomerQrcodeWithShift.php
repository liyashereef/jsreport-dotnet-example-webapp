<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerQrcodeWithShift extends Model
{
    protected $fillable = ['user_id','customer_id','shift_id','qrcode_id','time','no_of_attempts',
    'latitude','longitude','image','comments'];

    public function Qrcode()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerQrcodeLocation', 'qrcode_id', 'id');
    }
    public function QrcodeWithTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\CustomerQrcodeLocation', 'qrcode_id', 'id')->withTrashed();
    }

    public function customerQrcodeShifts(){

        return $this->hasMany(CustomerQrcodeSummary::class,'shift_id','shift_id');
    }

    public function user(){
        return $this->belongsTO('Modules\Admin\Models\User','user_id','id');
    }

    public function Customer(){
        return $this->belongsTo('Modules\Admin\Models\Customer','customer_id','id');
    }

    public function attachments()
    {
        return $this->hasMany('Modules\Timetracker\Models\CustomerQrcodeAttachment','qrcode_with_shift_id','id');
    }

    public function shift()
    {
        return $this->belongsTo('Modules\Timetracker\Models\EmployeeShift','shift_id','id');
    }

}
