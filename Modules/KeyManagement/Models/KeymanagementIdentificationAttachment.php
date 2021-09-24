<?php

namespace Modules\KeyManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeymanagementIdentificationAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['key_log_detail_id','identification_id','identification_attachment_id','identification_attachment_path'];
    protected $dates = ['deleted_at', 'created_at','updated_at'];


    public function keyLog(){

        return $this->belongsTo('Modules\KeyManagement\Models\KeyLogDetail', 'key_log_detail_id','id')->withTrashed();
    }
}
