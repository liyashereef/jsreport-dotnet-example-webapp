<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateScreeningPersonalityTestQuestionOption extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_screening_personality_test_question_options';
    public $timestamps = true;
    protected $fillable = [];

    // To get question of an option
    public function question()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateScreeningPersonalityTestQuestion', 'id', 'question_id');
    }
}
