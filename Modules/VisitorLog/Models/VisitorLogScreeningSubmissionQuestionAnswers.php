<?php

namespace Modules\VisitorLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorLogScreeningSubmissionQuestionAnswers extends Model
{
    use SoftDeletes;
    protected $table = 'visitor_log_screening_submission_question_answers';
    protected $fillable = ['visitor_log_screening_submission_id','answer',
    'visitor_log_screening_template_question_id','visitor_log_screening_template_question_str',
    'visitor_log_screening_template_question_expected_answer'];

    public function VisitorLogScreeningTemplateQuestionWithTrashed(){
        return $this->hasMany('Modules\Admin\Models\VisitorLogScreeningTemplateQuestion'
        , 'id', 'visitor_log_screening_template_question_id')->withTrashed();;
    }
}
