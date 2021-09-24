<?php

namespace Modules\KeyManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyLogDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    
    protected $fillable = [
        'customer_key_detail_id',
        'checked_out_to',
        'checked_in_from',
        'checked_out_date_time',
        'checked_in_date_time',
        'company_name',
        'identification_id',
        'identification_attachment_id',
        'signature_attachment_id',
        'check_out_signature_path',
        'check_in_signature_attachment_id',
        'check_in_signature_path',
        'key_availablity_id',
        'notes',
        'check_in_notes',
        'updated_by',
        'created_by'
    ];
    protected $dates = ['deleted_at', 'created_at','updated_at','checked_out_date_time','checked_in_date_time'];


    public function keyInfo() {
        return $this->belongsTo('Modules\KeyManagement\Models\CustomerKeyDetail', 'customer_key_detail_id','id')->withTrashed();
    }

    public function identifications(){

        return $this->hasMany('Modules\KeyManagement\Models\KeymanagementIdentificationAttachment','key_log_detail_id','id')->withTrashed();

    }

    public function checkedoutUser(){

        return $this->belongsTo('Modules\Admin\Models\User', 'created_by','id')->withTrashed();

    }

    public function checkedinUser(){

        return $this->belongsTo('Modules\Admin\Models\User', 'updated_by','id')->withTrashed();
    }

    
}
