<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateJob extends Model
{

    use SoftDeletes;

    public $timestamps = true;
    protected $fillable = [
        'candidate_id',
        'job_id',
        'fit_assessment_why_apply_for_this_job',
        'brand_awareness_id',
        'status',
        'security_awareness_id',
        'candidate_status',
        'feedback_id',
        'average_score',
        'english_rating_id',
        'submitted_date',
        'interview_score',
        'interview_date',
        'interview_notes',
        'reference_score',
        'reference_date',
        'reference_notes'
    ];

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
    public function job()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Job', 'job_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function jobReassigned()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Job', 'job_reassigned_id', 'id');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function reassigned_job()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Job', 'job_reassigned_id', 'id');
    }

    /**
     *
     *
     */
    public function feedback()
    {
        return $this->belongsTo('Modules\Admin\Models\FeedbackLookup', 'feedback_id', 'id');
    }
    public function candidate_brand_awareness()
    {
        return $this->belongsTo('Modules\Admin\Models\CandidateBrandAwareness', 'brand_awareness_id', 'id');
    }
    public function candidate_security_awareness()
    {
        return $this->belongsTo('Modules\Admin\Models\CandidateSecurityAwarenes', 'security_awareness_id', 'id');
    }

    public function candidateTracking()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateTracking', 'candidatejob_id', 'id');
    }

    public function englishProficiency()
    {

        return $this->belongsTo('Modules\Admin\Models\EnglishRatingLookup', 'english_rating_id', 'id');
    }
}
