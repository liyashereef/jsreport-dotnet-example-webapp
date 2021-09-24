<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecCandidateMatchScore extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_match_scores';
    public $timestamps = true;
    protected $fillable = [
        'candidate_id',
        'job_id',
        'criteria_id',
        'criteria_weight',
        'premium',
        'mapping_value',
        'weighted_score'
    ];


    public function criteria()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecScoreCriteria', 'criteria_id', 'id');
    }
}
