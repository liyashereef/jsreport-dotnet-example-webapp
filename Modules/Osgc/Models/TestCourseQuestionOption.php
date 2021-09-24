<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TestCourseQuestionOption extends Model
{
    use SoftDeletes;
    protected $table='osgc_test_course_question_options';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['question_id','answer_option','answer_option','is_correct_answer'];
}
