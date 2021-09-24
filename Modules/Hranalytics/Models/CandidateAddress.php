<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateAddress extends Model
{

    public $timestamps = true;

    protected $fillable = ['candidate_id', 'address', 'from', 'to'];

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
