<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningPersonalityScore extends Model
{
    protected $fillable = ['candidate_id','EI','SN','TF','JP','score','order'];

    // Relation to get the description of each score
    public function score_type()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\MyersBriggsPersonalityType', 'score', 'type');
    }
}
