<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningPersonalityScore extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_screening_personality_scores';
    protected $fillable = ['candidate_id','EI','SN','TF','JP','score','order'];

    // Relation to get the description of each score
    public function score_type()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecMyersBriggsPersonalityType', 'score', 'type');
    }
}
