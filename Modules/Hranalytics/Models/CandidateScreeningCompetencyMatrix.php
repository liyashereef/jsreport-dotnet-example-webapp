<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateScreeningCompetencyMatrix extends Model
{
    protected $fillable = ['candidate_id','competency_matrix_lookup_id','competency_matrix_rating_lookup_id','notes'];
    protected $table = 'candidate_screening_competency_matrix';

    // Get corresponding candidate 
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate');

    }

    // Get competency matrix 
    public function competency_matrix()
    {
        return $this->belongsTo('Modules\Admin\Models\CompetencyMatrixLookup','competency_matrix_lookup_id')->withTrashed();

    }

    // Get competency matrix rating
    public function competency_matrix_rating()
    {
        return $this->belongsTo('Modules\Admin\Models\CompetencyMatrixRatingLookup','competency_matrix_rating_lookup_id')->withTrashed();

    }
}
