<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateMiscellaneouses extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'veteran_of_armedforce', 'service_number', 'canadian_force', 'enrollment_date', 'release_date', 'item_release_number', 'rank_on_release', 'military_occupation', 'reason_for_release', 'dismissed', 'explanation_dismissed', 'limitations', 'limitation_explain', 'criminal_convicted', 'offence', 'offence_date', 'offence_location', 'disposition_granted', 'career_interest', 'other_roles','is_indian_native','spouse_of_armedforce'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }
}
