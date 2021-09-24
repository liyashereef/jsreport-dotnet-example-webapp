<?php

namespace Modules\Timetracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerQrcodeAttachment extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['qrcode_with_shift_id', 'attachment_id'];
    

    public function attachment()
    {
        return $this->belongsTo('App\Models\Attachment', 'attachment_id', 'id');
    }

}
