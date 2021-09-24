<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TestCourseQuestion extends Model
{
    use SoftDeletes;
    protected $table='osgc_test_course_questions';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['question_master_id','test_question','is_mandatory_display','is_mandatory_display'];
    public function test_question_options()
    {
        return $this->hasMany('Modules\Osgc\Models\TestCourseQuestionOption', 'question_id', 'id');
    }

}
