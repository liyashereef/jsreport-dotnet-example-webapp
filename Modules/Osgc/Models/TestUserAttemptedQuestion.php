<?php

namespace Modules\Osgc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TestUserAttemptedQuestion extends Model
{
    use SoftDeletes;
    protected $table='osgc_test_user_attempted_questions';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $fillable = ['test_user_result_id','test_course_question_id','test_course_question_option_id','is_correct_answer'];
}
