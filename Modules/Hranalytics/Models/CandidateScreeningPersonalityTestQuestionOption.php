<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningPersonalityTestQuestionOption extends Model
{
    protected $fillable = [];

    // To get question of an option
    public function question()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestion', 'id', 'question_id');
    }
}
