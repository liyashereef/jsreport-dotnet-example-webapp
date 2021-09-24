<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateCommissionairesUnderstanding extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_commissionaires_understandings';

    protected $fillable = ['candidate_id', 'commissionaires_understanding_lookups_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidateUnderstandingLookup()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCommissionairesUnderstandingLookup', 'commissionaires_understanding_lookups_id', 'id');
    }
}
