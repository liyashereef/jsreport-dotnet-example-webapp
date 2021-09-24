<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class OsgcUser extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    protected $fillable = [
        'course_id','course_section_id','first_name', 'last_name', 'email', 'password','is_veteran','indian_status','verification_token', 'active','email_verified','referral','last_login'
    ];

    public function UserPayment()
    {
        return $this->hasOne('Modules\Osgc\Models\CoursePayment', 'user_id', 'id')->where('status',1);
    }
    public function userSuccessPayments()
    {
        return $this->hasMany('Modules\Osgc\Models\CoursePayment', 'user_id', 'id')->where('status',1);
    }
    public function userPayments()
    {
        return $this->hasMany('Modules\Osgc\Models\CoursePayment', 'user_id', 'id');
    }
    public function userAllocatedCourses()
    {
        return $this->hasMany('Modules\Osgc\Models\AllocatedUserCourses', 'user_id', 'id');
    }
}
