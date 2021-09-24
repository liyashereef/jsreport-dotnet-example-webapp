<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecMatchScoreCriteriaMapping extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_match_score_criteria_mappings';
    public $timestamps = true;
    
    protected $fillable = [
        'criteria',
        'limit',
        'score'
    ];

    public function criteriaList()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecMatchScoreCriteria', 'criteria', 'criteria_id')->withTrashed();
    }
}
