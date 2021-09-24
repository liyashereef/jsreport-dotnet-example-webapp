<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecMatchScoreCriteria extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_rec';
    protected $table = 'rec_match_score_criterias';
    public $timestamps = true;

    protected $fillable = [
        'criteria_id',
        'weight',
    ];

    public function criteriaMapping()
    {
        $q = 'CAST(`limit` as UNSIGNED)';
        return $this->hasMany('Modules\Recruitment\Models\RecMatchScoreCriteriaMapping', 'criteria', 'criteria_id')->orderByRaw($q);
    }
    public function scoreCriteriaLookup()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecScoreCriteria', 'criteria_id', 'id');
    }
}
