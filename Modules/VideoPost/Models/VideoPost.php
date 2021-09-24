<?php

namespace Modules\VideoPost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoPost extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'video_path',
        'type',
        'created_by',
        'file_name',
        'file_type',
        'description',
        'video_uploaded_date'
    ];


    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id')->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User','created_by','id')->withTrashed();
    }



}
