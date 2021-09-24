<?php

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;

class RecJobRequiredExperience extends Model
{
    protected $connection = 'mysql_rec';
    public $timestamps = true;
    /**
     * Relation towards Job
     *
     * @return void
     */
    public function job()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecJob', 'job_id', 'id');
    }

    /**
     * Relation towards experience lookup
     *
     * @return void
     */
    public function experienceLookup()
    {
        return $this->belongsTo('Modules\Recruitment\Models\RecExperienceLookup', 'experience_id', 'id')->withTrashed();
    }
}
