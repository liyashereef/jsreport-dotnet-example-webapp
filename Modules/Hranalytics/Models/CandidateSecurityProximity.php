<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSecurityProximity extends Model
{

    public $timestamps = true;

    protected $fillable = ['candidate_id', 'driver_license', 'access_vehicle', 'access_public_transport', 'transportation_limitted', 'explanation_transport_limit'];

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
