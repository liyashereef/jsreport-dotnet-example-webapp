<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateAvailability extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'current_availability', 'days_required', 'shifts', 'availability_explanation', 'availability_start', 'understand_shift_availability', 'available_shift_work', 'explanation_restrictions'];

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
