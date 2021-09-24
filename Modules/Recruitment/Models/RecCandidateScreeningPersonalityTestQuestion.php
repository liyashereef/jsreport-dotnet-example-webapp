<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateScreeningPersonalityTestQuestion extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_screening_personality_test_questions';
    public $timestamps = true;
    protected $fillable = [];

    //To get options of a question
    public function options()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningPersonalityTestQuestionOption', 'question_id', 'id');
    }
}
