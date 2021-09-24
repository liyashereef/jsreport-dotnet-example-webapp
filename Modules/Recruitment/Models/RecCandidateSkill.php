<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
class RecCandidateSkill extends Model
{
    protected $connection = 'mysql_rec';
    protected $table = 'rec_candidate_skills';
    public $timestamps = true;
    protected $fillable = ['candidate_id', 'skill_id', 'skill_level'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public function candidate()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecCandidate', 'candidate_id', 'id');
    }

    /**
     *
     */
    public function skill_lookup()
    {
        return $this->setConnection('mysql')->belongsTo('Modules\Admin\Models\SkillLookup', 'skill_id', 'id');
    }
}
