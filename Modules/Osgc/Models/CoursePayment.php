<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CoursePayment extends Model
{
    use SoftDeletes;
    protected $table='osgc_course_payment';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['user_id','course_id','amount','transaction_id','status','started_time','end_time','payment_intent'];
    public function getActiveUser()
    {
        return $this->belongsTo(OsgcUser::class,'user_id','id')->where('active',1);

    }
    public function osgcCourses()
    {
        return $this->belongsTo(OsgcCourse::class,'course_id','id');
    }
    public function userAllocatedCourses                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     ()
    {
        return $this->hasMany('Modules\Osgc\Models\AllocatedUserCourses', 'user_id', 'user_id');
        
    }
}
