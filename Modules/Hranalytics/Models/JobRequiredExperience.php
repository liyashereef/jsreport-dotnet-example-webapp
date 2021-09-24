<?php

namespace Modules\Hranalytics\Models;

use Illuminate\Database\Eloquent\Model;

class JobRequiredExperience extends Model {

    /**
     * Relation towards Job
     *
     * @return void
     */
    public function job() {
        return $this->belongsTo('App\Models\Job', 'job_id', 'id');
    }

    /**
     * Relation towards experience lookup
     *
     * @return void
     */
    public function experienceLookup() {
        return $this->belongsTo('Modules\Admin\Models\ExperienceLookup', 'experience_id', 'id')->withTrashed();
    }

}
