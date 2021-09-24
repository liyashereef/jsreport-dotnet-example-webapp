<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateReferalAvailability extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $connection = 'mysql_rec';
    public $table = "rec_candidate_referals_availability";
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
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function jobPostFinding()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\JobPostFindingLookup', 'job_post_finding', 'id');
    }
}
