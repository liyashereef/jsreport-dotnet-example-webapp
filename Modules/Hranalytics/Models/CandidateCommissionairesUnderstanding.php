<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCommissionairesUnderstanding extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'commissionaires_understanding_lookups_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidateUnderstandingLookup()
    {
        return $this->belongsTo('Modules\Admin\Models\CommissionairesUnderstandingLookup', 'commissionaires_understanding_lookups_id', 'id');
    }
}
