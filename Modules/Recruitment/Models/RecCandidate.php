<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Passport\HasApiTokens;

class RecCandidate extends Authenticatable implements CanResetPassword
{

     use Notifiable,HasApiTokens;
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $guard = 'rec_candidate';
    protected $table = 'rec_candidates';
    public $timestamps = true;
    protected $appends=['full_address'];
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'dob',
        'email',
        'phone',
        'phone_cellular',
        'address',
        'city',
        'postal_code',
        'geo_location_lat',
        'geo_location_long',
        'smart_phone_type_id',
        'smart_phone_skill_level',
        'profile_image',
        'username',
        'password',
        'last_login',
        'status',
        'terms_accepted',
        'password_changed',
        'is_activated',
        'is_completed',
        'gender',
        'remember_token',
        'reset_token',
        'review_completed',
        'created_by',
        'updated_by',
        'is_converted'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function createdUser()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\User', 'created_by', 'id');
    }
    
    public function referalAvailibility()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateReferalAvailability', 'candidate_id', 'id');
    }
    public function tracking()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')->orderBy('process_lookups_id', 'desc');
    }
    public function userAccessTracking()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')->where('process_lookups_id', 2);
    }
    public function competencyTracking()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')->where('process_lookups_id', 6);
    }
    public function loginTracking()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')->where('process_lookups_id', 1);
    }
    public function lastTrack()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')
         ->whereHas('tracking_process')
         ->latest('process_lookups_id');
           // ->latest('completed_date');
    }

    public function uniformSubmittedTracking()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTracking', 'candidate_id', 'id')->where('process_lookups_id', 18);
    }

    // public function jobsApplied()
    // {
    //     return $this->hasMany('Modules\Recruitment\Models\RecCandidateJob', 'candidate_id', 'id')->where('status', '=', 'Applied')->orderby('id', 'desc');
    // }

    public function technicalSummary()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\SmartPhoneType', 'smart_phone_type_id', 'id');
    }

    public function candidateJobs()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateJobDetails', 'candidate_id', 'id');
    }
    public function personality_scores()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningPersonalityScore', 'candidate_id', 'id');
    }

    public function latestApplied()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateAwareness', 'candidate_id', 'id')->where('status', 'Applied')->orderby('id', 'desc')->latest();
    }

    public function miscellaneous()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateMiscellaneouses', 'candidate_id', 'id');
    }

    public function availability()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateAvailability', 'candidate_id', 'id');
    }
    public function awareness()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateAwareness', 'candidate_id', 'id');
    }

    public function wageExpectation()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateWageExpectation', 'candidate_id', 'id');
    }
    public function force()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateForceCertification', 'candidate_id', 'id');
    }

    public function guardingExperience()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateSecurityGuardingExperince', 'candidate_id', 'id');
    }

    public function attachements()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateAttachment', 'candidate_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateAddress', 'candidate_id', 'id');
    }

    public function securityclearance()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateSecurityClearances', 'candidate_id', 'id');
    }

    public function securityproximity()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateSecurityProximity', 'candidate_id', 'id');
    }

    public function experience()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateExperience', 'candidate_id', 'id');
    }

    public function employment_history()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateEmploymentHistory', 'candidate_id', 'id');
    }

    public function references()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateReference', 'candidate_id', 'id');
    }

    public function educations()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateEducation', 'candidate_id', 'id');
    }

    public function languages()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateLanguage', 'candidate_id', 'id');
    }

    public function screening_questions()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningQuestion', 'candidate_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateSkill', 'candidate_id', 'id');
    }

    public function termination()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateTermination', 'candidate_id', 'id');
    }

    public function personality_inventories()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningPersonalityInventory', 'candidate_id', 'id');
    }

    public function personality_sums()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningPersonalitySum', 'candidate_id', 'id');
    }

    public function competency_matrix()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningCompetencyMatrix', 'candidate_id', 'id');
    }

    public function comissionaires_understanding()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateCommissionairesUnderstanding', 'candidate_id', 'id');
    }
    /**
     * Get full name of user
     *
     * @return void
     */
    public function getFullAddressAttribute()
    {
        return ucfirst($this->address) . ' ' . ucfirst($this->city).' '.ucfirst($this->postal_code);
    }
    public function other_languages()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateScreeningOtherLanguages', 'candidate_id', 'id');
    }


    public function candidateShipment()
    {
        return $this->hasOne('Modules\Recruitment\Models\RecCandidateUniformShippmentDetail', 'candidate_id', 'id');
    }

    public function uniform_measurements()
    {
        return $this->hasMany('Modules\Recruitment\Models\RecCandidateUniformSize', 'candidate_id', 'id');
    }
}
