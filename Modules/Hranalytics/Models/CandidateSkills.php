<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateSkills extends Model
{
    public $timestamps = true;

    protected $fillable = ['candidate_id', 'skill_id', 'skill_level'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Hranalytics\Models\Candidate', 'candidate_id', 'id');
    }

    /**
     *
     */
    public function skill_lookup()
    {
        return $this->belongsTo('Modules\Admin\Models\SkillLookup', 'skill_id', 'id');
    }
}
