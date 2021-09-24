<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateTracking extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_trackings';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'completed_date','process_lookups_id', 'process_tab_id','notes','entered_by','job_id'];

    public function tracking_process()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecProcessSteps', 'process_lookups_id', 'id');
    }

    public function enteredBy()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'entered_by', 'id')->withTrashed();
    }
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    public function job()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_id', 'id');
    }

    // public function candidatejob()
    // {
    //     return $this->belongsTo('Modules\Recruitment\Models\RecCandidateJob', 'candidate_id', 'candidate_id');
    // }
}
