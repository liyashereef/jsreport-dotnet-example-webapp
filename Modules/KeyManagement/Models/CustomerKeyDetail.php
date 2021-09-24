<?php

namespace Modules\KeyManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerKeyDetail extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'customer_id', 
        'key_id', 
        'room_name',
        'attachment_id',
        'key_image_path',
        'key_availability',
        'active',
        'created_by'
    ];


    protected $dates = ['deleted_at', 'created_at','updated_at'];

    public function customer() {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id')->withTrashed();
    }
    
    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id')->withTrashed();
    }

    public function log(){

        return $this->hasMany('Modules\KeyManagement\Models\KeyLogDetail','customer_key_detail_id','id')->withTrashed();
    }
    public function info() {
        return $this->hasOne('Modules\KeyManagement\Models\KeyLogDetail')->latest();
    }

 
}
