<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSecurityClearance extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'born_outside_of_canada', 'work_status_in_canada', 'years_lived_in_canada', 'prepared_for_security_screening', 'no_clearance', 'no_clearance_explanation'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function candidateJob()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateJob', 'candidate_id', 'candidate_id');
    }
}
