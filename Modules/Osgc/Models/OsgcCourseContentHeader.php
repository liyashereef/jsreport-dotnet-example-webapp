<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OsgcCourseContentHeader extends Model
{
    use SoftDeletes;

    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name','sort_order','course_id','active'];
    public function courseUserCompletion()
    {
        return $this->hasOne('Modules\Osgc\Models\UserCourseCompletion', 'course_header_id', 'id')->where('user_id',\Auth::user()->id);

    }
}
