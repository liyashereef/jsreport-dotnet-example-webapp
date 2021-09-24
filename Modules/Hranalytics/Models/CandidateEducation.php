<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEducation extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'start_date_education', 'end_date_education', 'grade', 'program', 'school'];

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
