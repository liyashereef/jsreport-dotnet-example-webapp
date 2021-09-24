<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateJobDetails extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_job_details';
    public $timestamps = true;
    protected $fillable = [
        'candidate_id',
        'job_id',
        'rec_preference',
        'rec_match_score',
        'estimated_travel_time',
        'estimate_distance',
        'status',
        'recruiter_id',
    ];

    public function job()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_id', 'id');
    }


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }
    public function recruiter()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\User', 'recruiter_id', 'id');
    }
    public function statusLog()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateJobDetailsStatusLog', 'rec_job_details_id', 'id');
    }
}
