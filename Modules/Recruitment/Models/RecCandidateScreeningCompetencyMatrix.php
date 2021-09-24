<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningCompetencyMatrix extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_screening_competency_matrices';
    protected $fillable = ['candidate_id','competency_matrix_lookup_id','competency_matrix_rating_lookup_id','notes'];

    // Get corresponding candidate
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate');

    }

    // Get competency matrix
    public function competency_matrix()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCompetencyMatrixLookup','competency_matrix_lookup_id')->withTrashed();

    }

    // Get competency matrix rating
    public function competency_matrix_rating()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCompetencyMatrixRatingLookup','competency_matrix_rating_lookup_id')->withTrashed();

    }
}
