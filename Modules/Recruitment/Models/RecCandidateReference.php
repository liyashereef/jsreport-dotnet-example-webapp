<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecCandidateReference extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_references';
    public $timestamps = true;
    protected $fillable = [
        'candidate_id',
        'reference_name',
        'reference_employer',
        'reference_position',
        'contact_phone',
        'contact_email'
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