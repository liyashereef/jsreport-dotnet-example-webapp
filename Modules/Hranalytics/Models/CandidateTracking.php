<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateTracking extends Model
{

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'candidate_id',
        'lookup_id',
        'completion_date',
        'notes',
        'entered_by_id',
        'candidatejob_id',
    ];

    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    public function tracking_process()
    {
        return $this->belongsTo('Modules\Admin\Models\TrackingProcessLookup', 'lookup_id', 'id');
    }

    public function entered_by()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'entered_by_id', 'id')->withTrashed();
    }
    public function job()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Job', 'job_id', 'id');
    }
    public function candidatejob()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\CandidateJob', 'candidatejob_id', 'id');
    }
}
