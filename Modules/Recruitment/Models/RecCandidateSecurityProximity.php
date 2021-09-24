<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateSecurityProximity extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_security_proximities';
    protected $fillable = ['candidate_id', 'driver_license', 'access_vehicle', 'access_public_transport', 'transportation_limitted', 'explanation_transport_limit'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    // public function candidateJob()
    // {
    //     return $this->belongsTo('Modules\Recruitment\Models\RecCandidateJob', 'candidate_id', 'candidate_id');
    // }
}
