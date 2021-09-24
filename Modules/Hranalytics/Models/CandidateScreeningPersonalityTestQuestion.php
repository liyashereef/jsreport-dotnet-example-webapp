<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningPersonalityTestQuestion extends Model
{
    protected $fillable = [];

    //To get options of a question
    public function options()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestionOption', 'question_id', 'id');
    }
}
