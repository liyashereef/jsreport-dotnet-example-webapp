<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateMiscellaneouses extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_miscellaneouses';
    public $timestamps = true;

    protected $fillable = [
        'candidate_id',
        'veteran_of_armedforce',
        'service_number',
        'canadian_force',
        'enrollment_date',
        'release_date',
        'item_release_number',
        'rank_on_release',
        'military_occupation',
        'reason_for_release',
        'dismissed',
        'explanation_dismissed',
        'limitations',
        'limitation_explain',
        'criminal_convicted',
        'offence',
        'offence_date',
        'offence_location',
        'career_interest',
        'other_roles',
        'is_indian_native'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }
}
