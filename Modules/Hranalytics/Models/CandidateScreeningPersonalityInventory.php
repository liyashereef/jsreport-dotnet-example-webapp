<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningPersonalityInventory extends Model
{
    protected $fillable = ['candidate_id','question_id','question_option_id'];

    // Get corresponding question 
    public function question()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestion');

    }

    // Get corresponding option 
    public function answer()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestionOption','question_option_id');

    }
}
