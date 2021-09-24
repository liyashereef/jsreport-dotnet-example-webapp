<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateReferalAvailability extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    public $table = "candidate_referals_availability";
    protected $fillable = [
        'candidate_id',
        'job_post_finding',
        'sponser_email',
        'position_availibility',
        'floater_hours',
        'starting_time',
        'orientation'
    ];

    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function jobPostFinding()
    {
        return $this->belongsTo('Modules\Admin\Models\JobPostFindingLookup', 'job_post_finding', 'id');
    }
}
