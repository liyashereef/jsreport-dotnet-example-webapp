<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateJobInterview extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = ['user_id', 'interviewer_id', 'candidate_id', 'job_id', 'interview_date', 'interview_notes'];

    public function interviewers()
    {
        return $this->belongsTo('Modules\Admin\Models\User', 'interviewer_id', 'id');
    }

}
