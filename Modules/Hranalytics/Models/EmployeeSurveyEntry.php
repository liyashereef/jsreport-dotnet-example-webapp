<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyEntry extends Model
{
    use SoftDeletes;
    protected $fillable = ["survey_id", "customer_id", "user_id", "created_by"];

    public function survey()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\EmployeeSurveyTemplate', 'survey_id', 'id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo('Modules\Admin\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'user_id', 'id')->withTrashed();
    }

    public function created_user()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'created_by', 'id')->withTrashed();
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function surveyAnswer()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyAnswer', 'entry_id', 'id')
        ->join('employee_survey_questions', 'employee_survey_answers.question_id', 'employee_survey_questions.id')
        ->orderBy('employee_survey_questions.sequence', 'asc');
    }
}
