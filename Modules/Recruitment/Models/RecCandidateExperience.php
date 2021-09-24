<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateExperience extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_experiences';
    protected $fillable = ['candidate_id', 'current_employee_commissionaries', 'employee_number', 'currently_posted_site', 'position', 'hours_per_week', 'applied_employment', 'position_applied', 'start_date_position_applied', 'end_date_position_applied', 'employed_by_corps', 'position_employed', 'start_date_employed', 'end_date_employed', 'location_employed', 'employee_num'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }
    public function division()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\DivisionLookup', 'location_employed', 'id');
    }

    // public function candidateJob()
    // {
    //     return $this->belongsTo('Modules\Recruitment\Models\RecCandidateJob', 'candidate_id', 'candidate_id');
    // }
}
