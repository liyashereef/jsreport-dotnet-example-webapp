<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningQuestion extends Model
{
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'question_id', 'answer', 'score'];

    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo('Modules\Admin\Models\CandidateScreeningQuestionLookup', 'question_id', 'id');
    }
}
