<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TestUserResult extends Model
{
    use SoftDeletes;
    protected $table='osgc_test_user_results';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['course_section_id', 'user_id', 'test_course_master_id','course_pass_percentage','total_questions',
    'total_attempted_questions','total_exam_score','is_exam_pass','score_percentage','status','submitted_at'];
   
}
