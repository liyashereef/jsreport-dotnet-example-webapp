<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSurveyQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = ["survey_id", "question", "answer_type", "sequence", "created_by"];

    /**
     * Relation to form
     *
     * @return type
     */
    public function templateAnswer()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EmployeeSurveyAnswer', 'question_id', 'id');
    }
}
