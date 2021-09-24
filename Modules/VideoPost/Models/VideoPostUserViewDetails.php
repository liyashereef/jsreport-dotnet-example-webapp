<?php

namespace Modules\VideoPost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VideoPostUserViewDetails extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['video_post_id','viewed_user_id','status'];

    public function createdBy()
    {
        return $this->belongsTo('Modules\VideoPost\Models\VideoPost','video_post_id','id')->withTrashed();
    }
}
