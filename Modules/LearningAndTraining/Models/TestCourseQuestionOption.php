<?php

namespace Modules\LearningAndTraining\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TestCourseQuestionOption extends Model
{
	use SoftDeletes;
    public $timestamps = true;
    protected $fillable = ['test_course_question_id','answer_option','is_correct_answer'];

       public function trainingQuestion()
    {
        return $this->belongsTo('Modules\LearningAndTraining\Models\TestCourseQuestion', 'test_course_question_id', 'id');
    }
}
