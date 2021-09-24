<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = ['name', 'first_name', 'last_name', 'dob', 'email', 'gender', 'phone_home', 'phone_cellular', 'address', 'city', 'postal_code', 'smart_phone_type_id', 'smart_phone_skill_level', 'profile_image'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function referalAvailibility()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateReferalAvailability', 'candidate_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateAddress', 'candidate_id', 'id');
    }
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function uniform_measurements()
    {
        return $this->hasMany('Modules\UniformScheduling\Models\UniformMeasurements', "candidate_id");
    }

    public function availability()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateAvailability', 'candidate_id', 'id');
    }

    /**
     * Relation to jobs of this candidate applied
     *
     * @return void
     */
    public function jobsApplied()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateJob', 'candidate_id', 'id')->where('status', '=', 'Applied')->orderby('id', 'desc');
    }

    /**
     * Latest job applied by this candidate
     *
     * @return void
     */
    public function latestJobApplied()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateJob', 'candidate_id', 'id')->where('status', 'Applied')->orderby('id', 'desc')->latest();
    }

    public function securityclearance()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateSecurityClearance', 'candidate_id', 'id');
    }
    public function guardingExperience()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateSecurityGuardingExperince', 'candidate_id', 'id');
    }

    public function force()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateForceCertification', 'candidate_id', 'id');
    }
    
    public function securityproximity()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateSecurityProximity', 'candidate_id', 'id');
    }
    public function wageExpectation()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateWageExpectation', 'candidate_id', 'id');
    }
    public function experience()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateExperience', 'candidate_id', 'id');
    }
    public function miscellaneous()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateMiscellaneouses', 'candidate_id', 'id');
    }
    public function employment_history()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateEmploymentHistory', 'candidate_id', 'id');
    }
    public function employment_history_latest()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateEmploymentHistory', 'candidate_id', 'id')
        ->orderBy('end_date','desc')->latest();
    }
    public function references()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateReference', 'candidate_id', 'id');
    }
    public function educations()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateEducation', 'candidate_id', 'id');
    }
    public function languages()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateLanguage', 'candidate_id', 'id');
    }
    public function screening_questions()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningQuestion', 'candidate_id', 'id');
    }
    public function personality_inventories()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningPersonalityInventory', 'candidate_id', 'id');
    }

    public function personality_sums()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningPersonalitySum', 'candidate_id', 'id');
    }

    public function personality_scores()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningPersonalityScore', 'candidate_id', 'id');
    }
    public function skills()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateSkills', 'candidate_id', 'id');
    }
    // Competency Matrix
    public function competency_matrix()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningCompetencyMatrix', 'candidate_id', 'id');
    }
    public function attachements()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateAttachment', 'candidate_id', 'id');
    }

    public function trackings()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateTracking', 'candidate_id', 'id');
    }
    public function interviewnote()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateJobInterview', 'candidate_id', 'id');
    }
    public function eventlog()
    {
        return $this->hasMany('Modules\Hranalytics\Models\EventLogEntry', 'candidate_id', 'id');
    }

    public function eventlog_score()
    {
        return $this->eventlog()->selectRaw('candidate_id,round(( SUM(score)/(COUNT(*)*2) * 100 ),2) as avg_score,COUNT(score) as prev_attempt')
            ->groupBy('candidate_id');
    }

    public function lastTrack()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateTracking', 'candidate_id', 'id')
            ->whereHas('tracking_process')
            // ->orderby('lookup_id', 'DESC')
            ->latest('lookup_id');
    }
    public function jobs()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateJob', 'candidate_id', 'id')->orderby('id', 'desc');
    }

    public function technicalSummary()
    {
        return $this->belongsTo('Modules\Admin\Models\SmartPhoneType', 'smart_phone_type_id', 'id');
    }

    public function technicalSummaryTrashed()
    {
        return $this->belongsTo('Modules\Admin\Models\SmartPhoneType', 'smart_phone_type_id', 'id')->withTrashed();
    }

    public function termination()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateTermination', 'candidate_id', 'id');
    }
    public function candidateEmployees()
    {
        return $this->hasOne('Modules\Hranalytics\Models\CandidateEmployee', 'candidate_id', 'id');
    }
    public function comissionaires_understanding()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateCommissionairesUnderstanding', 'candidate_id', 'id');
    }
    public function other_languages()
    {
        return $this->hasMany('Modules\Hranalytics\Models\CandidateScreeningOtherLanguages', 'candidate_id', 'id');
    }
}
