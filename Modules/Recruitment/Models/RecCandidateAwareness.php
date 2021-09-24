<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateAwareness extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_awareness';
    public $timestamps = true;
    protected $fillable = [
        'candidate_id',
        //'job_id',
        'prefered_hours_per_week',
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
        'reference_score',
        'interview_date',
        'interview_notes',
        'reference_date',
        'reference_notes'
    ];

    // public function job()
    // {
    //     return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_id', 'id');
    // }

    public function feedback()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecFeedbackLookups', 'feedback_id', 'id');
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function jobReassigned()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_reassigned_id', 'id');
    }

    public function candidate_brand_awareness()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecBrandAwareness', 'brand_awareness_id', 'id');
    }

    public function candidate_security_awareness()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecSecurityAwareness', 'security_awareness_id', 'id');
    }

    public function personality_scores()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningPersonalityScore', 'candidate_id', 'candidate_id');
    }
}
