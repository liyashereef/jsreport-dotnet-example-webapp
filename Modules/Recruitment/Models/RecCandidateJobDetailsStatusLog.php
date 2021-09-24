<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateJobDetailsStatusLog extends Model
{
    //
    public $timestamps = true;
    protected $connection = 'mysql_rec';
    protected $fillable = ['status','rec_job_details_id', 'datetime','recruiter_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function jobDetails()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidateJobDetails', 'rec_job_details_id', 'id');
    }
    public function recruiter()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\User', 'recruiter_id', 'id');
    }
}
