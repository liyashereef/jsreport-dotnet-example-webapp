<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateWageExpectation extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'candidate_id',
        'wage_expectations_from',
        'wage_expectations_to',
        'wage_last_hourly',
        'wage_last_hours_per_week',
        'current_paystub',
        'wage_last_provider',
        'wage_last_provider_other',
        'last_role_held',
        'explanation_wage_expectation',
        'security_provider_strengths',
        'security_provider_notes',
        'rate_experience'
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

    public function wageprovider()
    {
        return $this->belongsTo('Modules\Admin\Models\SecurityProviderLookup', 'wage_last_provider', 'id');
    }

    public function lastrole()
    {
        return $this->belongsTo('Modules\Admin\Models\PositionLookup', 'last_role_held', 'id');
    }
    public function rating()
    {
        return $this->belongsTo('Modules\Admin\Models\RateExperienceLookups', 'rate_experience', 'id');
    }

}
