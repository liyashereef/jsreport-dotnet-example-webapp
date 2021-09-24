<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningPersonalityInventory extends Model
{
    protected $connection = 'mysql_rec';
    protected $fillable = ['candidate_id','question_id','question_option_id'];

    // Get corresponding question
    public function question()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateScreeningPersonalityTestQuestion');
    }

    // Get corresponding option
    public function answer()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateScreeningPersonalityTestQuestionOption', 'question_option_id');
    }
}
