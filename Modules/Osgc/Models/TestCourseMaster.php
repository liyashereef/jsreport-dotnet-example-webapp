<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TestCourseMaster extends Model
{
    use SoftDeletes;
    protected $table='osgc_test_course_masters';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['course_id','osgc_course_section_id','exam_name','number_of_question','random_question','pass_percentage','active','created_by','created_at','updated_at'];
    public function test_questions()
    {
        return $this->hasMany('Modules\Osgc\Models\TestCourseQuestion', 'question_master_id', 'id');
    }
    public function course_section()
    {
        return $this->belongsTo('Modules\Osgc\Models\OsgcCourseContentSection', 'osgc_course_section_id', 'id');
    }
}
