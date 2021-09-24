<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningQuestion extends Model
{
    protected $connection = 'mysql_rec';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'question_id', 'answer', 'score'];

    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateScreeningQuestionLookups', 'question_id', 'id');
    }
}
