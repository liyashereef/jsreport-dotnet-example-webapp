<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEmploymentHistory extends Model
{
    public $timestamps = true;
    public $table = "candidate_employment_historys";

    protected $fillable = ['candidate_id', 'start_date', 'end_date', 'employer', 'role', 'reason', 'duties'];

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
