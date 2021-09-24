<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateEmploymentHistory extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    public $table = "rec_candidate_employment_histories";

    protected $fillable = ['candidate_id', 'start_date', 'end_date', 'employer', 'role', 'reason', 'duties'];

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
