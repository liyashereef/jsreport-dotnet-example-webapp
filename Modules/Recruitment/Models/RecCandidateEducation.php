<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateEducation extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_education';
    protected $fillable = ['candidate_id', 'start_date_education', 'end_date_education', 'grade', 'program', 'school'];

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
