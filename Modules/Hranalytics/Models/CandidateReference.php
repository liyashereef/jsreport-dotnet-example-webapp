<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateReference extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'reference_name', 'reference_employer', 'reference_position', 'contact_phone', 'contact_email'];

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
